const mySqlSingleton = require('../../database/MySqlConnection');
const WebAppActivityModel = require('../../model/web_app_activity.model');
const OrganizationAppWebModel = require('../../model/organization_app_web.model');
const moment = require('moment-timezone');

class DashboardModel {
  // ============= EMPLOYEE STATISTICS =============
  
  /**
   * Get total number of employees
   */
  async getTotalEmployees() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT COUNT(*) as total FROM employees WHERE role = "employee"'
    );
    return rows[0].total;
  }

  /**
   * Get number of present employees for a specific date
   */
  async getPresentEmployees(date) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT COUNT(DISTINCT employee_id) as total FROM employee_attendance WHERE date = ?',
      [date]
    );
    return rows[0].total;
  }

  /**
   * Get number of absent employees for a specific date
   */
  async getAbsentEmployees(date) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT COUNT(*) as total FROM employees 
       WHERE role = "employee" 
       AND id NOT IN (
         SELECT DISTINCT employee_id FROM employee_attendance WHERE date = ?
       )`,
      [date]
    );
    return rows[0].total;
  }

  // ============= ATTENDANCE OPERATIONS =============
  
  /**
   * Get all attendance records for a date range
   */
  async getAllAttendance(startDate, endDate) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT id as attendance_id, employee_id FROM employee_attendance WHERE date BETWEEN ? AND ?',
      [startDate, endDate]
    );
    return rows;
  }

  // ============= APPLICATION & WEBSITE USAGE =============
  
  /**
   * Get top applications by usage (grouped by application from organization_app_web)
   */
  async getTopApplications(startDate, endDate, limit = 10, attendanceIds = []) {
    const startDateNum = parseInt(startDate.split('-').join(''));
    const endDateNum = parseInt(endDate.split('-').join(''));
    
    const matchQuery = {
      yyyymmdd: { $gte: startDateNum, $lte: endDateNum },
      application_name: { $ne: null, $ne: "" }
    };
    
    if (attendanceIds.length > 0) {
      matchQuery.attendance_id = { $in: attendanceIds };
    }

    // Use aggregation with populate if application_id exists
    const result = await WebAppActivityModel.aggregate([
      { $match: matchQuery },
      {
        $group: {
          _id: "$application_name",
          application_id: { $first: "$application_id" },
          totalSeconds: { $sum: "$total_seconds" },
          activeSeconds: { $sum: "$active_seconds" },
          productiveSeconds: { $sum: "$productive_seconds" }
        }
      },
      { $sort: { totalSeconds: -1 } },
      { $limit: limit * 2 } // Get more for grouping
    ]);

    if (result.length === 0) {
      return [];
    }

    // Get organization applications for name normalization
    const orgApplications = await OrganizationAppWebModel.find({
      type: 1,
      name: { $exists: true }
    }).lean();

    // Create ID to name map
    const idToNameMap = {};
    orgApplications.forEach(org => {
      idToNameMap[org._id.toString()] = org.name;
    });

    // Normalize and group by organization app name
    const appStats = {};
    result.forEach(record => {
      let appName = record._id;
      
      // Try to get name from application_id first
      if (record.application_id && idToNameMap[record.application_id.toString()]) {
        appName = idToNameMap[record.application_id.toString()];
      } else {
        // Fallback to matching by name
        const matchingOrg = orgApplications.find(org => {
          const orgAppName = org.name.toLowerCase();
          const activityAppName = record._id.toLowerCase();
          return orgAppName === activityAppName || 
                 activityAppName.includes(orgAppName) || 
                 orgAppName.includes(activityAppName);
        });
        if (matchingOrg) {
          appName = matchingOrg.name;
        }
      }

      if (!appStats[appName]) {
        appStats[appName] = {
          name: appName,
          totalSeconds: 0,
          activeSeconds: 0,
          productiveSeconds: 0
        };
      }
      
      appStats[appName].totalSeconds += record.totalSeconds || 0;
      appStats[appName].activeSeconds += record.activeSeconds || 0;
      appStats[appName].productiveSeconds += record.productiveSeconds || 0;
    });

    // Convert to array and format
    return Object.values(appStats)
      .sort((a, b) => b.totalSeconds - a.totalSeconds)
      .slice(0, limit)
      .map(app => ({
        name: app.name,
        totalSeconds: app.totalSeconds,
        activeSeconds: app.activeSeconds,
        productiveSeconds: app.productiveSeconds,
        hours: Math.floor(app.totalSeconds / 3600),
        minutes: Math.floor((app.totalSeconds % 3600) / 60),
        seconds: app.totalSeconds % 60
      }));
  }

  /**
   * Get top websites by usage (grouped by domain from organization_app_web)
   */
  async getTopWebsites(startDate, endDate, limit = 10, attendanceIds = []) {
    const startDateNum = parseInt(startDate.split('-').join(''));
    const endDateNum = parseInt(endDate.split('-').join(''));
    
    const matchQuery = {
      yyyymmdd: { $gte: startDateNum, $lte: endDateNum },
      url: { $ne: null, $ne: "" }
    };
    
    if (attendanceIds.length > 0) {
      matchQuery.attendance_id = { $in: attendanceIds };
    }

    // Use aggregation to group by URL
    const result = await WebAppActivityModel.aggregate([
      { $match: matchQuery },
      {
        $group: {
          _id: "$url",
          domain_id: { $first: "$domain_id" },
          totalSeconds: { $sum: "$total_seconds" },
          activeSeconds: { $sum: "$active_seconds" },
          productiveSeconds: { $sum: "$productive_seconds" }
        }
      },
      { $sort: { totalSeconds: -1 } },
      { $limit: limit * 2 } // Get more for grouping
    ]);

    if (result.length === 0) {
      return [];
    }

    // Get organization websites for domain normalization
    const orgWebsites = await OrganizationAppWebModel.find({
      type: 2,
      name: { $exists: true }
    }).lean();

    // Create ID to domain map
    const idToDomainMap = {};
    orgWebsites.forEach(org => {
      idToDomainMap[org._id.toString()] = org.name;
    });

    // Normalize and group by organization domain
    const domainStats = {};
    result.forEach(record => {
      let domainName = record._id;
      
      // Try to get domain from domain_id first
      if (record.domain_id && idToDomainMap[record.domain_id.toString()]) {
        domainName = idToDomainMap[record.domain_id.toString()];
      } else {
        // Fallback to matching by URL
        const matchingOrg = orgWebsites.find(org => {
          const orgDomain = org.name.toLowerCase();
          const activityUrl = record._id.toLowerCase();
          return activityUrl.includes(orgDomain) || orgDomain.includes(activityUrl);
        });
        
        if (matchingOrg) {
          domainName = matchingOrg.name;
        } else {
          // Extract domain from URL
          try {
            const urlObj = new URL(record._id.startsWith('http') ? record._id : `https://${record._id}`);
            domainName = urlObj.hostname.replace('www.', '');
          } catch {
            domainName = record._id;
          }
        }
      }

      if (!domainStats[domainName]) {
        domainStats[domainName] = {
          name: domainName,
          totalSeconds: 0,
          activeSeconds: 0,
          productiveSeconds: 0
        };
      }
      
      domainStats[domainName].totalSeconds += record.totalSeconds || 0;
      domainStats[domainName].activeSeconds += record.activeSeconds || 0;
      domainStats[domainName].productiveSeconds += record.productiveSeconds || 0;
    });

    // Convert to array and format
    return Object.values(domainStats)
      .sort((a, b) => b.totalSeconds - a.totalSeconds)
      .slice(0, limit)
      .map(domain => ({
        name: domain.name,
        totalSeconds: domain.totalSeconds,
        activeSeconds: domain.activeSeconds,
        productiveSeconds: domain.productiveSeconds,
        hours: Math.floor(domain.totalSeconds / 3600),
        minutes: Math.floor((domain.totalSeconds % 3600) / 60),
        seconds: domain.totalSeconds % 60
      }));
  }

  // ============= EMPLOYEE ACTIVITY STATISTICS =============
  
  /**
   * Get employees ranked by active hours
   */
  async getEmployeesByActiveHours(startDate, endDate, limit = 5, sortOrder = 'desc', attendanceIds = []) {
    const pool = await mySqlSingleton.getPool();
    const startDateNum = parseInt(startDate.split('-').join(''));
    const endDateNum = parseInt(endDate.split('-').join(''));
    
    const matchQuery = {
      yyyymmdd: { $gte: startDateNum, $lte: endDateNum }
    };
    
    if (attendanceIds.length > 0) {
      matchQuery.attendance_id = { $in: attendanceIds };
    }

    const activityData = await WebAppActivityModel.aggregate([
      { $match: matchQuery },
      {
        $group: {
          _id: "$employee_id",
          totalActiveSeconds: { $sum: "$active_seconds" },
          totalSeconds: { $sum: "$total_seconds" }
        }
      },
      { $sort: { totalActiveSeconds: sortOrder === 'desc' ? -1 : 1 } },
      { $limit: limit }
    ]);

    // Get employee details
    if (activityData.length === 0) return [];
    
    const employeeIds = activityData.map(item => item._id);
    const [employees] = await pool.query(
      `SELECT id, first_name, last_name, email, employee_code 
       FROM employees 
       WHERE id IN (?)`,
      [employeeIds]
    );

    // Merge employee data with activity data
    const result = activityData.map(activity => {
      const employee = employees.find(emp => emp.id === activity._id);
      return {
        employeeId: activity._id,
        firstName: employee?.first_name || 'Unknown',
        lastName: employee?.last_name || '',
        email: employee?.email || '',
        employeeCode: employee?.employee_code || '',
        totalActiveSeconds: activity.totalActiveSeconds,
        totalSeconds: activity.totalSeconds,
        hours: Math.floor(activity.totalActiveSeconds / 3600),
        minutes: Math.floor((activity.totalActiveSeconds % 3600) / 60),
        seconds: activity.totalActiveSeconds % 60
      };
    });

    return result;
  }

  /**
   * Get employees ranked by productive hours
   */
  async getEmployeesByProductiveHours(startDate, endDate, limit = 5, sortOrder = 'desc', attendanceIds = []) {
    const pool = await mySqlSingleton.getPool();
    const startDateNum = parseInt(startDate.split('-').join(''));
    const endDateNum = parseInt(endDate.split('-').join(''));
    
    const matchQuery = {
      yyyymmdd: { $gte: startDateNum, $lte: endDateNum }
    };
    
    if (attendanceIds.length > 0) {
      matchQuery.attendance_id = { $in: attendanceIds };
    }

    const activityData = await WebAppActivityModel.aggregate([
      { $match: matchQuery },
      {
        $group: {
          _id: "$employee_id",
          totalProductiveSeconds: { $sum: "$productive_seconds" },
          totalSeconds: { $sum: "$total_seconds" }
        }
      },
      { $sort: { totalProductiveSeconds: sortOrder === 'desc' ? -1 : 1 } },
      { $limit: limit }
    ]);

    // Get employee details
    if (activityData.length === 0) return [];
    
    const employeeIds = activityData.map(item => item._id);
    const [employees] = await pool.query(
      `SELECT id, first_name, last_name, email, employee_code 
       FROM employees 
       WHERE id IN (?)`,
      [employeeIds]
    );

    // Merge employee data with activity data
    const result = activityData.map(activity => {
      const employee = employees.find(emp => emp.id === activity._id);
      return {
        employeeId: activity._id,
        firstName: employee?.first_name || 'Unknown',
        lastName: employee?.last_name || '',
        email: employee?.email || '',
        employeeCode: employee?.employee_code || '',
        totalProductiveSeconds: activity.totalProductiveSeconds,
        totalSeconds: activity.totalSeconds,
        hours: Math.floor(activity.totalProductiveSeconds / 3600),
        minutes: Math.floor((activity.totalProductiveSeconds % 3600) / 60),
        seconds: activity.totalProductiveSeconds % 60
      };
    });

    return result;
  }
}

module.exports = new DashboardModel();

