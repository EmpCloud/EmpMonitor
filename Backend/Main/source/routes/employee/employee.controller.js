const EmployeeModel = require('./employee.model');
const crypto = require('crypto');
const jwt = require('jsonwebtoken');
const moment = require('moment-timezone');
require('dotenv').config();

// Encryption setup
const algorithm = 'aes-256-cbc';
const key = crypto.createHash('sha256').update(process.env.JWT_SECRET).digest('base64').substr(0, 32);

class EmployeeController {
  // ============= HELPER METHODS =============
  
  /**
   * Decrypt password or verify
   */
  decryptPassword(userPassword, encryptedPassword) {
    try {
      const [ivHex, encrypted] = encryptedPassword.split(':');
      const iv = Buffer.from(ivHex, 'hex');
      const decipher = crypto.createDecipheriv(algorithm, key, iv);
      let decrypted = decipher.update(encrypted, 'hex', 'utf8');
      decrypted += decipher.final('utf8');
      
      if (userPassword) {
        return userPassword === decrypted;
      }
      return decrypted;
    } catch (error) {
      console.error('Error in decryptPassword: ', error);
      throw error;
    }
  }

  /**
   * Generate JWT token
   */
  generateToken(payload) {
    return jwt.sign(payload, process.env.JWT_SECRET, { expiresIn: '1d' });
  }

  /**
   * Get login user data from token
   */
  async getLoginUserData(req) {
    try {
      const authHeader = req.headers['authorization'];
      const token = authHeader && authHeader.split(' ')[1];
      
      if (!token) {
        throw new Error('Token not provided');
      }

      const user = jwt.verify(token, process.env.JWT_SECRET);
      return user;
    } catch (error) {
      console.error('Error in getLoginUserData: ', error);
      throw error;
    }
  }

  // ============= AUTHENTICATION =============
  
  /**
   * Employee login
   */
  async employeeLogin(req, res) {
    try {
      const { email, password } = req.body;
      const employee = await EmployeeModel.getEmployeeByEmail(email);
      
      if (employee && this.decryptPassword(password, employee.password)) {
        const token = this.generateToken({ id: employee.id, role: employee.role });
        delete employee.password;
        res.json({ token, ...employee });
      } else {
        res.status(401).json({ error: 'Invalid credentials' });
      }
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= EMPLOYEE OPERATIONS =============
  
  /**
   * Get employee data
   */
  async getEmployee(req, res) {
    try {
      const employee = await EmployeeModel.getEmployeeById(req.user.id);
      
      if (!employee) {
        return res.status(404).json({ error: 'Employee not found' });
      }
      
      res.json(employee);
    } catch (err) {
      return res.status(500).json({ message: 'Internal server error', error: err.message });
    }
  }

  /**
   * Get employee by ID
   */
  async getEmployeeById(req, res) {
    try {
      const { id } = req.params;
      let resp = await EmployeeModel.getEmployeeById(id);
      let password = this.decryptPassword(null, resp.password);
      resp.password = password;
      res.status(200).json({ message: 'Success', data: resp });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Update employee
   */
  async updateEmployee(req, res) {
    try {
      const { id } = await this.getLoginUserData(req);
      const { firstName, lastName, role, mobileNumber, employeeCode, timeZone } = req.body;

      if (!firstName || !lastName || !role) {
        return res.status(400).json({ error: 'Missing required fields' });
      }

      await EmployeeModel.updateEmployee(id, {
        firstName,
        lastName,
        role,
        mobileNumber,
        employeeCode,
        timeZone,
      });
      
      res.status(200).json({ message: 'Employee updated' });
    } catch (err) {
      return res.status(500).json({ message: 'Internal server error', error: err.message });
    }
  }

  // ============= ATTENDANCE =============
  
  /**
   * Get attendance
   */
  async getAttendance(req, res) {
    try {
      const authHeader = req.headers.authorization;
      
      if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({ message: 'Authorization header missing or malformed' });
      }
      
      const token = authHeader.split(' ')[1];
      const { id } = jwt.decode(token);
      
      let { start_date, end_date, skip = 0, limit = 10 } = req.body; 
      
      start_date = moment(start_date).format("YYYY-MM-DD");
      end_date = moment(end_date).format("YYYY-MM-DD");

      const [attendanceRecords, attendanceRecordCount] = await Promise.all([
        EmployeeModel.getAllAttendanceById(id, start_date, end_date, +skip, +limit),
        EmployeeModel.getAttendanceCount(start_date, end_date, id)
      ]);

      return res.json({
        code: 200,
        data: {
          totalCount: attendanceRecordCount,
          data: attendanceRecords
        },
        message: "Success"
      });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= WEB APP ACTIVITY =============
  
  /**
   * Get web app activity
   */
  async getWebAppActivity(req, res) {
    try {
      let employeeId = +req.user.id;
      
      // Support both GET (query params) and POST (body params)
      const params = req.method === 'GET' ? req.query : req.body;
      let { startDate, endDate, type = 1 } = params;
      
      startDate = moment(startDate).format('YYYY-MM-DD');
      
      if (moment(startDate).isSame(endDate)) {
        endDate = moment(endDate).endOf('day').format('YYYY-MM-DD');
      }
      
      if (!employeeId) {
        return res.status(400).json({ message: 'employeeId and startDate are required.' });
      }

      // Fetch attendance records to get attendanceIds
      const attendanceRecords = await EmployeeModel.getAllAttendanceById(
        employeeId,
        startDate,
        endDate,
        0,
        5000
      );
      const attendanceIds = attendanceRecords.map(record => record.id);

      const activityRecords = await EmployeeModel.getWebAppActivityFiltered(
        employeeId,
        startDate,
        endDate,
        +type,
        attendanceIds
      );
      
      return res.json({ code: 200, data: activityRecords, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }
}

module.exports = new EmployeeController();
