const mySqlSingleton = require('../../database/MySqlConnection');
const WebAppActivityModel = require('../../model/web_app_activity.model');
const _ = require('underscore');

class ProductivityReportsModel {
  /**
   * Get employees assigned to a manager
   * Note: This assumes an assigned_employees table exists. If not, return empty array.
   */
  async getEmployeeAssignedToManager(managerId) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query(
        'SELECT employee_id FROM assigned_employees WHERE manager_id = ?',
        [managerId]
      );
      return rows;
    } catch (error) {
      // If table doesn't exist, return empty array
      console.log('assigned_employees table may not exist:', error.message);
      return [];
    }
  }

  /**
   * Filter employees by location, department, and employee_id
   */
  async filterEmployees({ location_id, department_id, employee_id, organization_id, specificEmployeeIds = null }) {
    const pool = await mySqlSingleton.getPool();
    let query = 'SELECT id, location_id, department_id FROM employees WHERE role = "employee"';
    const conditions = [];
    const params = [];

    if (specificEmployeeIds && specificEmployeeIds.length > 0) {
      conditions.push('id IN (?)');
      params.push(specificEmployeeIds);
    } else if (employee_id) {
      conditions.push('id = ?');
      params.push(employee_id);
    } else if (department_id) {
      conditions.push('department_id = ?');
      params.push(department_id);
    } else if (location_id && location_id !== 'All' && location_id !== 'all') {
      conditions.push('location_id = ?');
      params.push(location_id);
    }

    if (conditions.length > 0) {
      query += ' AND ' + conditions.join(' AND ');
    }

    const [rows] = await pool.query(query, params);
    return rows;
  }

  /**
   * Get employee productivity data with hierarchical filtering
   */
  async getEmployeeProductivityData(
    organization_id,
    location_id,
    department_id,
    employee_id,
    startDate,
    endDate,
    skip,
    limit,
    specificEmployeeId = null,
    managerId = null
  ) {
    const startDateNum = parseInt(startDate.split('-').join(''));
    const endDateNum = parseInt(endDate.split('-').join(''));

    // First, get employee IDs from MySQL based on filters
    const employees = await this.filterEmployees({
      location_id,
      department_id,
      employee_id,
      organization_id,
      specificEmployeeIds: specificEmployeeId
    });

    if (employees.length === 0) {
      return [];
    }

    const employeeIds = _.pluck(employees, 'id');

    // Build match query for MongoDB
    const matchQuery = {
      organization_id: organization_id,
      employee_id: { $in: employeeIds },
      yyyymmdd: { $gte: startDateNum, $lte: endDateNum }
    };

    // Build aggregation pipeline
    const pipeline = [{ $match: matchQuery }];

    // Determine grouping based on hierarchical filters
    let groupId = {};
    if (location_id === 'All' || location_id === 'all') {
      // Need to get location_id from employees - will handle in post-processing
      // For now, group by employee_id and we'll aggregate by location in post-processing
      groupId = '$employee_id';
    } else if (location_id && !department_id && !employee_id) {
      // Group by department_id - need employee department mapping
      groupId = '$employee_id';
    } else if (location_id && department_id && !employee_id) {
      // Group by employee_id
      groupId = '$employee_id';
    } else if (location_id && department_id && employee_id) {
      // Group by date (yyyymmdd)
      groupId = { employee_id: '$employee_id', yyyymmdd: '$yyyymmdd' };
    } else {
      groupId = '$employee_id';
    }

    // Add grouping stage
    pipeline.push({
      $group: {
        _id: groupId,
        employee_id: { $first: '$employee_id' },
        yyyymmdd: { $first: '$yyyymmdd' },
        productive_duration: { $sum: '$productive_seconds' },
        non_productive_duration: { $sum: '$unproductive_seconds' },
        neutral_duration: { $sum: '$neutral_seconds' },
        idle_duration: { $sum: '$idle_seconds' },
        total_logged_duration: { $sum: '$total_seconds' },
        count: { $sum: 1 }
      }
    });

    // Add calculated fields
    pipeline.push({
      $addFields: {
        office_time: {
          $add: [
            '$productive_duration',
            '$non_productive_duration',
            '$neutral_duration',
            '$idle_duration'
          ]
        },
        computer_activities_time: {
          $add: [
            '$productive_duration',
            '$non_productive_duration',
            '$neutral_duration'
          ]
        }
      }
    });

    // Sort by date (yyyymmdd) descending, then by employee_id
    pipeline.push({ $sort: { yyyymmdd: -1, employee_id: 1 } });

    let result = await WebAppActivityModel.aggregate(pipeline);

    // Post-process to group by location/department if needed
    if (location_id === 'All' || location_id === 'all') {
      // Group by location_id
      const locationMap = {};
      employees.forEach(emp => {
        if (!locationMap[emp.location_id]) {
          locationMap[emp.location_id] = [];
        }
        locationMap[emp.location_id].push(emp.id);
      });

      const groupedResult = {};
      result.forEach(item => {
        const empId = item.employee_id || item._id;
        const emp = employees.find(e => e.id === empId);
        if (emp) {
          const locId = emp.location_id;
          if (!groupedResult[locId]) {
            groupedResult[locId] = {
              _id: locId,
              productive_duration: 0,
              non_productive_duration: 0,
              neutral_duration: 0,
              idle_duration: 0,
              total_logged_duration: 0,
              office_time: 0,
              computer_activities_time: 0,
              count: 0
            };
          }
          groupedResult[locId].productive_duration += item.productive_duration;
          groupedResult[locId].non_productive_duration += item.non_productive_duration;
          groupedResult[locId].neutral_duration += item.neutral_duration;
          groupedResult[locId].idle_duration += item.idle_duration;
          groupedResult[locId].total_logged_duration += item.total_logged_duration;
          groupedResult[locId].office_time += item.office_time;
          groupedResult[locId].computer_activities_time += item.computer_activities_time;
          groupedResult[locId].count += item.count;
        }
      });
      result = Object.values(groupedResult);
    } else if (location_id && !department_id && !employee_id) {
      // Group by department_id
      const deptMap = {};
      employees.forEach(emp => {
        if (!deptMap[emp.department_id]) {
          deptMap[emp.department_id] = [];
        }
        deptMap[emp.department_id].push(emp.id);
      });

      const groupedResult = {};
      result.forEach(item => {
        const empId = item.employee_id || item._id;
        const emp = employees.find(e => e.id === empId);
        if (emp) {
          const deptId = emp.department_id;
          if (!groupedResult[deptId]) {
            groupedResult[deptId] = {
              _id: deptId,
              productive_duration: 0,
              non_productive_duration: 0,
              neutral_duration: 0,
              idle_duration: 0,
              total_logged_duration: 0,
              office_time: 0,
              computer_activities_time: 0,
              count: 0
            };
          }
          groupedResult[deptId].productive_duration += item.productive_duration;
          groupedResult[deptId].non_productive_duration += item.non_productive_duration;
          groupedResult[deptId].neutral_duration += item.neutral_duration;
          groupedResult[deptId].idle_duration += item.idle_duration;
          groupedResult[deptId].total_logged_duration += item.total_logged_duration;
          groupedResult[deptId].office_time += item.office_time;
          groupedResult[deptId].computer_activities_time += item.computer_activities_time;
          groupedResult[deptId].count += item.count;
        }
      });
      result = Object.values(groupedResult);
    } else if (location_id && department_id && employee_id) {
      // Format date for response
      result = result.map(item => {
        const yyyymmdd = item.yyyymmdd || (item._id && item._id.yyyymmdd) || null;
        const dateStr = yyyymmdd ? 
          `${String(yyyymmdd).substring(0, 4)}-${String(yyyymmdd).substring(4, 6)}-${String(yyyymmdd).substring(6, 8)}` : 
          null;
        return {
          ...item,
          _id: yyyymmdd,
          date: dateStr,
          employee_id: item.employee_id || (item._id && item._id.employee_id)
        };
      });
    }

    // Apply pagination
    if (skip) {
      result = result.slice(skip);
    }
    if (limit) {
      result = result.slice(0, limit);
    }

    return result;
  }

  /**
   * Get employee productivity data count
   */
  async getEmployeeProductivityDataCount(
    organization_id,
    location_id,
    department_id,
    employee_id,
    startDate,
    endDate,
    specificEmployeeId = null,
    managerId = null
  ) {
    const startDateNum = parseInt(startDate.split('-').join(''));
    const endDateNum = parseInt(endDate.split('-').join(''));

    // Get employee IDs from MySQL
    const employees = await this.filterEmployees({
      location_id,
      department_id,
      employee_id,
      organization_id,
      specificEmployeeIds: specificEmployeeId
    });

    if (employees.length === 0) {
      return [{ myCount: 0 }];
    }

    const employeeIds = _.pluck(employees, 'id');

    const matchQuery = {
      organization_id: organization_id,
      employee_id: { $in: employeeIds },
      yyyymmdd: { $gte: startDateNum, $lte: endDateNum }
    };

    const pipeline = [{ $match: matchQuery }];

    // Determine grouping based on hierarchical filters
    let groupId = {};
    if (location_id === 'All' || location_id === 'all') {
      groupId = '$employee_id';
    } else if (location_id && !department_id && !employee_id) {
      groupId = '$employee_id';
    } else if (location_id && department_id && !employee_id) {
      groupId = '$employee_id';
    } else if (location_id && department_id && employee_id) {
      groupId = { employee_id: '$employee_id', yyyymmdd: '$yyyymmdd' };
    } else {
      groupId = '$employee_id';
    }

    pipeline.push({
      $group: {
        _id: groupId
      }
    });

    // Post-process grouping for location/department
    let uniqueGroups = new Set();
    const groupedIds = pipeline[pipeline.length - 1].$group._id;
    
    if (location_id === 'All' || location_id === 'all') {
      employees.forEach(emp => {
        uniqueGroups.add(emp.location_id);
      });
    } else if (location_id && !department_id && !employee_id) {
      employees.forEach(emp => {
        uniqueGroups.add(emp.department_id);
      });
    } else {
      // Count distinct groups from aggregation
      const tempResult = await WebAppActivityModel.aggregate(pipeline);
      return [{ myCount: tempResult.length }];
    }

    return [{ myCount: uniqueGroups.size }];
  }

  /**
   * Get location data by IDs
   */
  async getLocationData(locationIds, organizationId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT id, location_name as name FROM locations WHERE id IN (?)',
      [locationIds]
    );
    return rows;
  }

  /**
   * Get department data by IDs
   */
  async getDepartmentData(departmentIds, organizationId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT id, name FROM departments WHERE id IN (?)',
      [departmentIds]
    );
    return rows;
  }

  /**
   * Get employee data by IDs (including user details)
   */
  async getEmployeeData(employeeIds, organizationId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT 
        e.id, 
        CONCAT(e.first_name, ' ', e.last_name) as name,
        e.first_name,
        e.last_name,
        e.email,
        e.computer_name,
        e.username
      FROM employees e
      WHERE e.id IN (?)`,
      [employeeIds]
    );
    return rows;
  }
}

module.exports = new ProductivityReportsModel();
