const mySqlSingleton = require('../../database/MySqlConnection');
const Organization_App_Web = require('../../model/organization_app_web.model');
const WebAppActivityModel = require('../../model/web_app_activity.model');

class AdminModel {
  // ============= ADMIN OPERATIONS =============
  
  /**
   * Get admin by email
   */
  async getAdminByEmail(email) {
    const pool = await mySqlSingleton.getPool();
    const query = 'SELECT * FROM admins WHERE email = ? LIMIT 1';
    const [rows] = await pool.execute(query, [email.toLowerCase().trim()]);
    return rows[0] || null;
  }

  /**
   * Get admin license count
   */
  async getAdminLicenseCount(adminId) {
    const pool = await mySqlSingleton.getPool();
    const query = 'SELECT license FROM admins WHERE id = ? LIMIT 1';
    const [rows] = await pool.execute(query, [adminId]);
    return rows.length === 0 ? null : rows[0].license;
  }

  /**
   * Update admin license
   */
  async updateAdminLicense(adminId, newLicense) {
    const pool = await mySqlSingleton.getPool();
    const query = 'UPDATE admins SET license = ? WHERE id = ?';
    const [result] = await pool.execute(query, [newLicense, adminId]);
    return result.affectedRows > 0;
  }

  // ============= EMPLOYEE OPERATIONS =============
  
  /**
   * Register new employee
   */
  async registerEmployee(employee) {
    const pool = await mySqlSingleton.getPool();
    const { firstName, lastName, email, password, mobileNumber, employeeCode, timeZone, role, departmentId, locationId } = employee;
    const [result] = await pool.query(
      'INSERT INTO employees (first_name, last_name, email, password, mobile_number, employee_code, time_zone, role, department_id, location_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
      [firstName, lastName, email, password, mobileNumber, employeeCode, timeZone, role, departmentId, locationId]
    );
    return result.insertId;
  }

  /**
   * Get all employees with pagination
   */
  async getAllEmployees(skip, limit, name, count = 0) {
    const pool = await mySqlSingleton.getPool();
    let query = '';
    const params = ['employee'];

    if (count) {
      query = `
        SELECT COUNT(*) AS total 
        FROM employees 
        INNER JOIN departments ON employees.department_id = departments.id 
        WHERE employees.role = ?
      `;
    } else {
      query = `
        SELECT employees.*, departments.name AS department_name 
        FROM employees 
        INNER JOIN departments ON employees.department_id = departments.id 
        WHERE employees.role = ?
      `;
    }

    if (name) {
      query += ` AND (employees.first_name LIKE ? OR employees.last_name LIKE ? OR employees.email LIKE ?)`;
      params.push(`%${name}%`, `%${name}%`, `%${name}%`);
    }

    if (!count) {
      query += ` LIMIT ? OFFSET ?`;
      params.push(limit, skip);
    }

    const [rows] = await pool.query(query, params);
    return count ? (rows[0]?.total || 0) : rows;
  }

  /**
   * Count total employees
   */
  async countEmployees() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT COUNT(*) as total FROM employees');
    return rows[0].total;
  }

  /**
   * Get employee by ID
   */
  async getEmployeeById(id) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT employees.*, departments.name AS department_name
       FROM employees
       INNER JOIN departments ON employees.department_id = departments.id
       WHERE employees.id = ?`,
      [id]
    );
    return rows[0];
  }

  /**
   * Update employee
   */
  async updateEmployee(id, employee) {
    const pool = await mySqlSingleton.getPool();
    const { firstName, lastName, role, mobileNumber, employeeCode, timeZone, email, password } = employee;
    await pool.query(
      'UPDATE employees SET first_name = ?, last_name = ?, role = ?, mobile_number = ?, employee_code = ?, time_zone = ?, email = ?, password = ? WHERE id = ?',
      [firstName, lastName, role, mobileNumber, employeeCode, timeZone, email, password, id]
    );
  }

  /**
   * Delete employee
   */
  async deleteEmployee(id) {
    const pool = await mySqlSingleton.getPool();
    await pool.query('DELETE FROM employee_attendance WHERE employee_id = ?', [id]);
    await pool.query('DELETE FROM employees WHERE id = ?', [id]);
  }

  // ============= ATTENDANCE OPERATIONS =============
  
  /**
   * Get all attendance records
   */
  async getAllAttendance(start_date, end_date, skip, limit, employee_id, name, count) {
    const pool = await mySqlSingleton.getPool();
    let query = '';
    
    if (count) {
      query = 'SELECT COUNT(*) AS total FROM employee_attendance ea JOIN employees e ON e.id = ea.employee_id WHERE date BETWEEN ? AND ?';
    } else {
      query = 'SELECT *, ea.id as attendance_id FROM employee_attendance ea JOIN employees e ON e.id = ea.employee_id WHERE date BETWEEN ? AND ?';
    }
    
    const params = [start_date, end_date];

    if (employee_id) {
      query += ' AND employee_id = ?';
      params.push(employee_id);
    }

    if (name) {
      query += ' AND (e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ?)';
      params.push(`%${name}%`, `%${name}%`, `%${name}%`);
    }

    if (!count) {
      query += ' LIMIT ?, ?';
      params.push(skip, limit);
    }

    const [rows] = await pool.query(query, params);
    return count ? (rows[0]?.total || 0) : rows;
  }

  /**
   * Get attendance by employee ID
   */
  async getAllAttendanceById(id, start_date, end_date, skip, limit) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT * FROM employee_attendance WHERE employee_id = ? AND date BETWEEN ? AND ? LIMIT ?, ?',
      [id, start_date, end_date, skip, limit]
    );
    return rows;
  }

  /**
   * Get attendance count
   */
  async getAttendanceCount(start_date, end_date, employee_id) {
    const pool = await mySqlSingleton.getPool();
    let query = 'SELECT COUNT(*) AS total FROM employee_attendance ea JOIN employees e ON e.id = ea.employee_id WHERE date BETWEEN ? AND ?';
    const params = [start_date, end_date];

    if (employee_id) {
      query += ' AND employee_id = ?';
      params.push(employee_id);
    }

    const [rows] = await pool.query(query, params);
    return rows[0].total;
  }

  /**
   * Get total usage from MongoDB
   */
  getTotalUsage(empIds, attendanceIds) {
    return WebAppActivityModel.aggregate([
      {
        $match: {
          employee_id: { $in: empIds },
          attendance_id: { $in: attendanceIds }
        }
      },
      {
        $group: {
          _id: "$employee_id",
          office_usage: { $sum: "$total_seconds" },
          active_usage: { $sum: "$active_seconds" },
          idle_usage: { $sum: "$idle_seconds" },
          productive_usage: { $sum: "$productive_seconds" },
          unproductive_usage: { $sum: "$unproductive_seconds" },
          neutral_usage: { $sum: "$neutral_seconds" },
          attendance_id: { $first: "$attendance_id" }
        }
      }
    ]);
  }

  // ============= WEB APP ACTIVITY OPERATIONS =============
  
  /**
   * Get filtered web app activity
   */
  async getWebAppActivityFiltered(employeeId, startDate, endDate, type, attendanceIds = []) {
    const query = {
      employee_id: employeeId,
      attendance_id: { $in: attendanceIds }
    };

    if (type === 1) {
      query.url = { $ne: null, $eq: "" };
    }

    if (type === 2) {
      query.url = { $ne: null, $ne: "" };
    }

    return WebAppActivityModel.find(query);
  }

  // ============= DEPARTMENT OPERATIONS =============
  
  /**
   * Get all departments
   */
  async getDepartments() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM departments');
    return rows;
  }

  /**
   * Get department by ID
   */
  async getDepartmentById(id) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM departments WHERE id = ?', [id]);
    return rows[0];
  }

  /**
   * Get department by name
   */
  async getDepartmentByName(name) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM departments WHERE name = ?', [name]);
    return rows[0];
  }

  /**
   * Add department
   */
  async addDepartment(departmentName, locationId) {
    const pool = await mySqlSingleton.getPool();
    const [result] = await pool.query(
      'INSERT INTO departments (name, location_id) VALUES (?, ?)',
      [departmentName, locationId]
    );
    return result.insertId;
  }

  /**
   * Update department
   */
  async updateDepartment(id, departmentName, locationId) {
    const pool = await mySqlSingleton.getPool();
    if (locationId) {
      await pool.query('UPDATE departments SET name = ?, location_id = ? WHERE id = ?', [departmentName, locationId, id]);
    } else {
      await pool.query('UPDATE departments SET name = ? WHERE id = ?', [departmentName, id]);
    }
  }

  /**
   * Delete department
   */
  async deleteDepartment(id) {
    const pool = await mySqlSingleton.getPool();
    await pool.query('DELETE FROM departments WHERE id = ?', [id]);
  }

  /**
   * Check if department is used by employees
   */
  async isDepartmentUsed(departmentId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT COUNT(*) AS count FROM employees WHERE department_id = ?', [departmentId]);
    return rows[0].count > 0;
  }

  // ============= LOCATION OPERATIONS =============
  
  /**
   * Get all locations
   */
  async getLocations() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM locations');
    return rows.map(row => {
      delete row.binary_column;
      return row;
    });
  }

  /**
   * Get location by ID
   */
  async getLocationById(id) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM locations WHERE id = ?', [id]);
    return rows.map(row => {
      delete row.binary_column;
      return row;
    });
  }

  /**
   * Get location by name
   */
  async getLocationByName(locationName) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT * FROM locations WHERE LOWER(location_name) = LOWER(?)',
      [locationName.trim()]
    );
    return rows.map(row => {
      delete row.binary_column;
      return row;
    });
  }

  /**
   * Add location
   */
  async addLocation(locationName) {
    const pool = await mySqlSingleton.getPool();
    const [result] = await pool.query('INSERT INTO locations (location_name) VALUES (?)', [locationName]);
    return result.insertId;
  }

  /**
   * Update location
   */
  async updateLocation(id, locationName) {
    const pool = await mySqlSingleton.getPool();
    return pool.query('UPDATE locations SET location_name = ? WHERE id = ?', [locationName, id]);
  }

  /**
   * Delete location
   */
  async deleteLocation(id) {
    const pool = await mySqlSingleton.getPool();
    return pool.query('DELETE FROM locations WHERE id = ?', [id]);
  }

  /**
   * Check if location has departments
   */
  async hasDepartmentsInLocation(locationId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT COUNT(*) as departmentCount FROM departments WHERE location_id = ?',
      [locationId]
    );
    return rows[0].departmentCount > 0;
  }

  // ============= REPORTS OPERATIONS =============
  
  /**
   * Filter employees by criteria
   */
  async filterEmployee({ employee_id, department_id, location_id }) {
    const pool = await mySqlSingleton.getPool();
    let query = `
      SELECT employees.first_name, employees.last_name, employees.email, employees.id, 
             departments.name AS department_name, locations.location_name, employees.role 
      FROM employees
      INNER JOIN departments ON employees.department_id = departments.id
      INNER JOIN locations ON employees.location_id = locations.id
    `;
    const conditions = [];
    const values = [];

    if (employee_id) {
      conditions.push(`employees.id = ?`);
      values.push(employee_id);
    }
    if (department_id) {
      conditions.push(`employees.department_id = ?`);
      values.push(department_id);
    }
    if (location_id) {
      conditions.push(`employees.location_id = ?`);
      values.push(location_id);
    }

    if (conditions.length) {
      query += ` WHERE ` + conditions.join(' AND ');
    }

    return pool.query(query, values);
  }

  /**
   * Get reports from MongoDB
   */
  getReports({ employee_id, start_date, end_date, attendanceIds = [] }) {
    const query = {
      employee_id: { "$in": employee_id },
      yyyymmdd: {
        "$gte": +start_date.split("-").join(""),
        "$lte": +end_date.split("-").join(""),
      }
    };

    if (attendanceIds.length > 0) {
      query.attendance_id = { "$in": attendanceIds };
    }

    return WebAppActivityModel.find(query).populate('employee_id').lean();
  }

  /**
   * Get reports count from MongoDB
   */
  getReportsCount({ employee_id, start_date, end_date, attendanceIds = [] }) {
    const query = {
      employee_id: { "$in": employee_id },
      yyyymmdd: {
        "$gte": +start_date.split("-").join(""),
        "$lte": +end_date.split("-").join(""),
      }
    };

    if (attendanceIds.length > 0) {
      query.attendance_id = { "$in": attendanceIds };
    }

    return WebAppActivityModel.countDocuments(query);
  }

  // ============= LOCALIZATION OPERATIONS =============
  
  /**
   * Upsert localization data
   */
  async upsertLocalizationData({ organization_id, lang, timezone }) {
    const pool = await mySqlSingleton.getPool();
    const query = `
      UPDATE admins
      SET lang = ?, time_zone = ?
      WHERE id = ?
    `;
    const [result] = await pool.execute(query, [lang, timezone, organization_id]);
    return result.affectedRows > 0;
  }

  /**
   * Get localization data
   */
  async getLocalizationData(organization_id) {
    const pool = await mySqlSingleton.getPool();
    const query = 'SELECT lang, time_zone FROM admins WHERE id = ? LIMIT 1';
    const [rows] = await pool.execute(query, [organization_id]);
    return rows.length === 0 ? null : rows[0];
  }

  // ============= PRODUCTIVITY RULES OPERATIONS =============
  
  /**
   * Get productivity rules
   */
  async getProductivityRules({ skip, limit, search, organization_id }) {
    const match = { organization_id: organization_id };
    if (search) match.search = { $regex: search, $options: 'i' };
    
    return Organization_App_Web.aggregate([
      { $match: match },
      { $skip: parseInt(skip) || 0 },
      { $limit: parseInt(limit) || 10 }
    ]);
  }

  /**
   * Get productivity rules count
   */
  async getProductivityRulesCount({ search, organization_id }) {
    const match = { organization_id: organization_id };
    if (search) match.search = { $regex: search, $options: 'i' };
    
    const result = await Organization_App_Web.aggregate([
      { $match: match },
      { $count: 'total' }
    ]);
    
    return result.length > 0 ? result[0].total : 0;
  }

  /**
   * Get productivity by ID
   */
  getProductivityById(id) {
    return Organization_App_Web.findOne({ _id: id });
  }
}

module.exports = new AdminModel();
