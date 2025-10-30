const mySqlSingleton = require('../../database/MySqlConnection');
const WebAppActivityModel = require('../../model/web_app_activity.model');

class EmployeeModel {
  // ============= EMPLOYEE OPERATIONS =============
  
  /**
   * Get employee by email
   */
  async getEmployeeByEmail(email) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM employees WHERE email = ?', [email]);
    return rows[0];
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
    const { firstName, lastName, role, mobileNumber, employeeCode, timeZone } = employee;
    
    await pool.query(
      'UPDATE employees SET first_name = ?, last_name = ?, role = ?, mobile_number = ?, employee_code = ?, time_zone = ? WHERE id = ?',
      [firstName, lastName, role, mobileNumber, employeeCode, timeZone, id]
    );
  }

  // ============= ATTENDANCE OPERATIONS =============
  
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

  // ============= WEB APP ACTIVITY OPERATIONS =============
  
  /**
   * Get filtered web app activity
   */
  async getWebAppActivityFiltered(employeeId, startDate, endDate, type) {
    const query = {
      employee_id: employeeId,
    };
    
    if (startDate) {
      query.yyyymmdd = { $gte: startDate.split('-').join('') };
    }

    if (endDate) {
      query.end_time = { $lte: endDate.split('-').join('') };
    }

    if (type === 1) {
      query.url = { $ne: null, $eq: "" };
    }

    if (type === 2) {
      query.url = { $ne: null, $ne: "" };
    }

    return WebAppActivityModel.find(query);
  }
}

module.exports = new EmployeeModel();
