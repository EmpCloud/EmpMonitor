const mySqlSingleton = require('../../database/MySqlConnection');
const WebAppActivityModel = require('../../model/web_app_activity.model');

class EmployeeModel {
  async registerEmployee(employee) {
    try {
      const pool = await mySqlSingleton.getPool();
      const { firstName, lastName, email, password, mobileNumber, employeeCode, timeZone, role, departmentId, locationId } = employee;
      const [result] = await pool.query('INSERT INTO employees (first_name, last_name, email, password, mobile_number, employee_code, time_zone, role, department_id, location_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [firstName, lastName, email, password, mobileNumber, employeeCode, timeZone, role, departmentId, locationId]);
      return result.insertId;
    } catch (error) {
      console.error('Error registering employee:', error);
      throw error;
    }
  }
  async getEmployeeById(id) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query(
        `SELECT employees.*, departments.name AS department_name
       FROM employees
       INNER JOIN departments ON employees.department_id = departments.id
       WHERE employees.id = ?`,
        [id]
      );
      return rows[0];
    } catch (error) {
      console.error('Error getting employee by id:', id, error);
      throw error;
    }
  }

  async getEmployeeByEmail(email) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query('SELECT * FROM employees WHERE email = ?', [email]);
      return rows[0];
    } catch (error) {
      if (error.code === 'AUTH_SWITCH_PLUGIN_ERROR') {
        console.error(
          'Database Authentication Error: Please change your MySQL user authentication plugin to mysql_native_password.'
        );
        throw new Error(
          'Database Authentication Error: Incorrect Authentication Plugin. Please check server logs.'
        );
      }
      console.error('Error getting employee by email:', email, error);
      throw error;
    }
  }

  async updateEmployee(id, employee) {
    try {
      const pool = await mySqlSingleton.getPool();
      const { firstName, lastName, role, mobileNumber, employeeCode, timeZone } = employee;
      await pool.query(
        'UPDATE employees SET first_name = ?, last_name = ?, role = ?, mobile_number = ?, employee_code = ?, time_zone = ? WHERE id = ?',
        [firstName, lastName, role, mobileNumber, employeeCode, timeZone, id]
      );
    } catch (error) {
      console.error('Error updating employee with id:', id, error);
      throw error;
    }
  }

  async getAllEmployees(skip, limit, name, count = 0) {
    try {
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
      if (count) return rows[0]?.total || 0;
      return rows;
    } catch (error) {
      console.error('Error getting all employees:', error);
      throw error;
    }
  }


  async countEmployees() {
    try {
      const pool = await mySqlSingleton.getPool();

      // Use COUNT(*) to get the total number of employees
      const [rows] = await pool.query('SELECT COUNT(*) as total FROM employees');

      return rows[0].total;
    } catch (error) {
      console.error('Error counting employees:', error);
      throw error;
    }
  }

  async deleteEmployee(id) {
    try {
      const pool = await mySqlSingleton.getPool();
      await pool.query('DELETE FROM employee_attendance WHERE employee_id = ?', [id]);
      await pool.query('DELETE FROM employees WHERE id = ?', [id]);
    } catch (error) {
      console.error('Error deleting employee with id:', id, error);
      throw error;
    }
  }

  async getAllAttendance(start_date, end_date, skip, limit, employee_id, name, count) {
    try {
      const pool = await mySqlSingleton.getPool();

      let query = '';
      if (count) {
        query = 'SELECT COUNT(*) AS total FROM employee_attendance ea JOIN employees e ON e.id = ea.employee_id WHERE date BETWEEN ? AND ?';
      } else {
        query = 'SELECT * FROM employee_attendance ea JOIN employees e ON e.id = ea.employee_id WHERE date BETWEEN ? AND ?';
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
      if (count) return rows[0]?.total || 0;
      return rows;
    } catch (error) {
      console.error('Error fetching attendance records:', error);
      throw error;
    }
  }

  async getAttendanceCount(start_date, end_date, employee_id) {
    try {
      const pool = await mySqlSingleton.getPool();

      let query = 'SELECT COUNT(*) AS total FROM employee_attendance ea JOIN employees e ON e.id = ea.employee_id WHERE date BETWEEN ? AND ?';
      const params = [start_date, end_date];

      if (employee_id) {
        query += ' AND employee_id = ?';
        params.push(employee_id);
      }

      const [rows] = await pool.query(query, params);
      return rows[0].total;
    } catch (error) {
      console.error('Error fetching attendance count:', error);
      throw error;
    }
  }


  async getAllAttendanceById(id, start_date, end_date, skip, limit) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query(
        'SELECT * FROM employee_attendance WHERE employee_id = ? AND date BETWEEN ? AND ? LIMIT ?, ?',
        [id, start_date, end_date, skip, limit]
      );
      return rows;
    } catch (error) {
      console.error('Error fetching attendance records:', error);
      throw error;
    }
  }


  async getWebAppActivityFiltered(employeeId, startDate, endDate, type) {
    try {
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

      const webAppActivities = await WebAppActivityModel.find(query);
      return webAppActivities;
    } catch (error) {
      console.error('Error fetching filtered web app activity records from MongoDB:', error);
      throw error;
    }
  }
  async getDepartments() {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query('SELECT * FROM departments');
      return rows;
    } catch (error) {
      console.error('Error fetching departments:', error);
      throw error;
    }
  }

  async addDepartment(departmentName, locationId) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [result] = await pool.query(
        'INSERT INTO departments (name, location_id) VALUES (?, ?)',
        [departmentName, locationId]
      );
      return result.insertId;
    } catch (error) {
      console.error('Error adding department:', error);
      throw error;
    }
  }


  async updateDepartment(id, departmentName) {
    try {
      const pool = await mySqlSingleton.getPool();
      await pool.query('UPDATE departments SET name = ? WHERE id = ?', [departmentName, id]);
    } catch (error) {
      console.error('Error updating department with id:', id, error);
      throw error;
    }
  }

  async deleteDepartment(id) {
    try {
      const pool = await mySqlSingleton.getPool();
      await pool.query('DELETE FROM departments WHERE id = ?', [id]);
    } catch (error) {
      console.error('Error deleting department with id:', id, error);
      throw error;
    }
  }

  async getDepartmentByName(name) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query('SELECT * FROM departments WHERE name = ?', [name]);
      return rows[0];
    } catch (error) {
      console.error('Error getting department by name:', name, error);
      throw error;
    }
  }

  async isDepartmentUsed(departmentId) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query('SELECT COUNT(*) AS count FROM employees WHERE department_id = ?', [departmentId]);
      return rows[0].count > 0;
    } catch (error) {
      console.error('Error checking if department with id is used:', departmentId, error);
      throw error;
    }
  }

  async getDepartmentById(id) {
    try {
      const pool = await mySqlSingleton.getPool();
      const [rows] = await pool.query('SELECT * FROM departments WHERE id = ?', [id]);
      return rows[0];
    } catch (error) {
      throw error;
    }
  }

  async getLocations() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM locations');
    return rows.map(row => {
      delete row.binary_column;
      return row;
    });
  }

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

  async addLocation(locationName) {
    const pool = await mySqlSingleton.getPool();
    const result = await pool.query('INSERT INTO locations (location_name) VALUES (?)', [locationName]);
    return result.insertId;
  }

  async getLocationById(id) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query('SELECT * FROM locations WHERE id = ?', [id]);
    return rows.map(row => {
      delete row.binary_column;
      return row;
    });
  }

  async updateLocation(id, locationName) {
    const pool = await mySqlSingleton.getPool();
    return pool.query('UPDATE locations SET location_name = ? WHERE id = ?', [locationName, id]);
  }

  async deleteLocation(id) {
    const pool = await mySqlSingleton.getPool();
    return pool.query('DELETE FROM locations WHERE id = ?', [id]);
  }

  async hasDepartmentsInLocation(locationId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT COUNT(*) as departmentCount FROM departments WHERE location_id = ?',
      [locationId]
    );

    return rows[0].departmentCount > 0;
  }

  getEmployeeTimesheet({ dates, employee_id }) {
    return WebAppActivityModel.aggregate([
      {
        $match: {
          yyyymmdd: { $in: dates },
          employee_id: +employee_id
        }
      },
      {
        $group: {
          _id: {
            employee_id: "$employee_id",
            yyyymmdd: "$yyyymmdd"
          },
          total_seconds: { $sum: "$total_seconds" },
          active_seconds: { $sum: "$active_seconds" },
          idle_seconds: { $sum: "$idle_seconds" },
          yyyymmdd: { $first: "$yyyymmdd" },
        }
      },
      {
        $project: {
          _id: 0,
          employee_id: "$_id.employee_id",
          yyyymmdd: "$yyyymmdd",
          total_seconds: 1,
          active_seconds: 1,
          idle_seconds: 1
        }
      }
    ]);
  }

  getReports({ employee_id, start_date, end_date }) {
    return WebAppActivityModel.find({
      employee_id: { "$in": employee_id },
      yyyymmdd: {
        "$gte": +start_date.split("-").join(""),
        "$lte": +end_date.split("-").join(""),
      }
    }).populate('employee_id') // assumes employee_id is a ref
    .lean();
  }

  getReportsCount({ employee_id, start_date, end_date }) {
    return WebAppActivityModel.countDocuments({
      employee_id: { "$in": employee_id },
      yyyymmdd: {
        "$gte": +start_date.split("-").join(""),
        "$lte": +end_date.split("-").join(""),
      }
    });
  }

  async filterEmployee({ employee_id, department_id, location_id }) {
    const pool = await mySqlSingleton.getPool();

    let query = `
      SELECT employees.first_name, employees.last_name, employees.email, employees.id, departments.name AS department_name, locations.location_name, employees.role FROM employees
      INNER JOIN departments ON employees.department_id = departments.id
      INNER JOIN locations ON employees.location_id = locations.id
    `;
    const conditions = [];
    const values = [];

    if (employee_id) {
      conditions.push(`id = ?`);
      values.push(employee_id);
    }
    if (department_id) {
      conditions.push(`department_id = ?`);
      values.push(department_id);
    }
    if (location_id) {
      conditions.push(`location_id = ?`);
      values.push(location_id);
    }

    if (conditions.length) {
      query += ` WHERE ` + conditions.join(' AND ');
    }

    return pool.query(query, values);
  }

  getTotalUsage(empIds, start_date, end_date) {
    return WebAppActivityModel.aggregate([
      {
        $match: {
          employee_id: { $in: empIds },
          yyyymmdd: { $gte: +start_date.split("-").join(""), $lte: +end_date.split("-").join("") }
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
        }
      }
    ]);
  }
}

module.exports = new EmployeeModel();