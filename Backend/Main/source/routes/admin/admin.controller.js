const AdminModel = require('./admin.model');
const crypto = require('crypto');
const jwt = require('jsonwebtoken');
const moment = require('moment-timezone');
const _ = require('underscore');
require('dotenv').config();

// Encryption setup
const algorithm = 'aes-256-cbc';
const key = crypto.createHash('sha256').update(process.env.JWT_SECRET).digest('base64').substr(0, 32);

const DEFAULT_ADMIN_EMAIL = process.env.DEFAULT_ADMIN_EMAIL || 'admin@example.com';
const DEFAULT_ADMIN_PASSWORD = process.env.DEFAULT_ADMIN_PASSWORD || 'password123';

class AdminController {
  // ============= HELPER METHODS =============
  
  /**
   * Encrypt password
   */
  encryptPassword(password) {
    try {
      const iv = crypto.randomBytes(16);
      const cipher = crypto.createCipheriv(algorithm, key, iv);
      let encrypted = cipher.update(password, 'utf8', 'hex');
      encrypted += cipher.final('hex');
      return `${iv.toString('hex')}:${encrypted}`;
    } catch (error) {
      console.error('Error in encryptPassword: ', error);
      throw error;
    }
  }

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
   * Admin login
   */
  async adminLogin(req, res) {
    try {
      const { email, password } = req.body;
      
      // Check default admin credentials
      if (email === DEFAULT_ADMIN_EMAIL && password === DEFAULT_ADMIN_PASSWORD) {
        const token = this.generateToken({ id: 1, role: 'admin', license: 5 });
        return res.json({ token, role: 'admin', license: 5 });
      }
      
      // Check database admin credentials
      const admin = await AdminModel.getAdminByEmail(email);
      
      if (admin && this.decryptPassword(password, admin.password) && admin.role === 'admin') {
        const token = this.generateToken({ id: admin.id, role: admin.role, license: admin.license });
        const adminResponse = { ...admin };
        delete adminResponse.password;
        return res.json({ token, ...adminResponse });
      } else {
        return res.status(401).send('Invalid credentials');
      }
    } catch (error) {
      console.error('Error during admin login:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= EMPLOYEE MANAGEMENT =============
  
  /**
   * Register new employee
   */
  async adminRegister(req, res) {
    try {
      // Verify admin role
      if (!req.user || req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Forbidden: Admin role required for this action.' });
      }

      const { firstName, lastName, email, password, mobileNumber, employeeCode, timeZone, departmentId, locationId } = req.body;
      
      // Validate email
      if (!email || email.split('@').length !== 2 || !/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,}$/.test(email)) {
        return res.status(400).json({ error: 'Invalid email format.' });
      }
      
      // Validate required fields
      if (!firstName || !lastName || !email || !password) {
        return res.status(400).json({ error: 'Validation Error: Missing required fields (first name, last name, email, password).' });
      }
      
      if (!departmentId) {
        return res.status(400).json({ error: 'Department ID is required.' });
      }
      
      if (!locationId) {
        return res.status(400).json({ error: 'Location ID is required.' });
      }

      // Check if department and location exist
      const departmentExists = await AdminModel.getDepartmentById(departmentId);
      if (!departmentExists) {
        return res.status(404).json({ error: 'Department not found.' });
      }

      const locationExists = await AdminModel.getLocationById(locationId);
      if (!locationExists) {
        return res.status(404).json({ error: 'Location not found.' });
      }

      // Check license limit
      const adminId = req.user.id;
      const adminLicenseLimit = await AdminModel.getAdminLicenseCount(adminId);
      const currentEmployeeCount = await AdminModel.countEmployees();

      if (adminLicenseLimit !== null && currentEmployeeCount >= adminLicenseLimit) {
        return res.status(403).json({ message: `License limit of ${adminLicenseLimit} employees reached. Cannot register more employees.` });
      }
      
      const hashedPassword = this.encryptPassword(password);

      const employeeId = await AdminModel.registerEmployee({
        firstName,
        lastName,
        email,
        password: hashedPassword,
        mobileNumber,
        employeeCode,
        timeZone,
        role: 'employee',
        departmentId,
        locationId
      });

      res.status(201).json({ 
        message: 'Employee registered successfully!', 
        id: employeeId,
        firstName,
        lastName,
        email,
        mobileNumber,
        employeeCode,
        timeZone,
        role: 'employee',
        departmentId,
        locationId 
      });
    } catch (error) {
      if (error.code === 'ER_DUP_ENTRY') {
        return res.status(409).json({ message: 'Email already registered.' });
      }
      console.error('Error during admin registration:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Get all employees
   */
  async getAllEmployees(req, res) {
    try {
      let { skip = 0, limit = 10, name } = req.query;

      const [employees, count] = await Promise.all([
        AdminModel.getAllEmployees(+skip, +limit, name),
        AdminModel.getAllEmployees(+skip, +limit, name, 1)
      ]);
   
      return res.status(200).json({ employees, totalCount: count });
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
      let resp = await AdminModel.getEmployeeById(id);
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
      const { role } = await this.getLoginUserData(req);
      if (role !== 'admin') {
        return res.status(400).json({ message: 'Admin can update employee', error: null });
      }

      const { employeeId, firstName, lastName, employeeRole, mobileNumber, employeeCode, timeZone, password, email } = req.body;
      
      if (!(employeeId && firstName && lastName && employeeRole && email && password && mobileNumber && employeeCode && timeZone)) {
        return res.json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
      }

      const hashedPassword = this.encryptPassword(password);
      const employeeData = {
        firstName,
        lastName,
        role: employeeRole,
        email,
        password: hashedPassword,
        mobileNumber,
        employeeCode,
        timeZone,
      };

      await AdminModel.updateEmployee(employeeId, employeeData);
      employeeData.employeeId = employeeId;
      
      return res.json({ code: 200, data: employeeData, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Delete single employee
   */
  async deleteEmployee(req, res) {
    try {
      const { id } = req.params;
      
      if (!id) {
        return res.status(400).json({ message: 'Invalid inputs' });
      }
      
      let result = await AdminModel.getEmployeeById(id);
      if (!result) {
        return res.status(404).json({ message: 'Employee not found' });
      }
      
      await AdminModel.deleteEmployee(id);
      res.status(200).json({ message: 'Employee deleted successfully' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Delete multiple employees
   */
  async deleteEmployees(req, res) {
    try {
      const { user_ids } = req.body;
      
      if (!user_ids || !Array.isArray(user_ids)) {
        return res.status(400).json({ message: 'Invalid inputs' });
      }
      
      let result = await Promise.all(user_ids.map(id => AdminModel.getEmployeeById(id)));
      
      if (!result.length) {
        return res.status(404).json({ message: 'Employee not found' });
      }
      
      await Promise.all(user_ids.map(id => AdminModel.deleteEmployee(id)));
      res.status(200).json({ message: 'Employee deleted successfully' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= ATTENDANCE =============
  
  /**
   * Get attendance records
   */
  async getAttendance(req, res) {
    try {
      let { start_date, end_date, skip = 0, limit = 10, employee_id, name } = req.body;
      
      if (employee_id) employee_id = parseInt(employee_id) || null;
      if (limit) limit = parseInt(limit) || 10;
      
      start_date = moment(start_date).format("YYYY-MM-DD");
      end_date = moment(end_date).format("YYYY-MM-DD");

      // Run both queries in parallel
      let [attendanceRecords, attendanceRecordCount] = await Promise.all([
        AdminModel.getAllAttendance(start_date, end_date, +skip, +limit, employee_id, name, 0),
        AdminModel.getAllAttendance(start_date, end_date, null, null, employee_id, name, 1)
      ]);

      let empIds = _.pluck(attendanceRecords, 'employee_id');
      let totalUsage = await AdminModel.getTotalUsage(empIds, start_date, end_date);

      attendanceRecords = attendanceRecords.map(record => {
        let tempUsage = totalUsage.find(i => i._id === record.employee_id);
        return {
          ...record,
          ...tempUsage,
          total_usage: moment(record.end_time).diff(moment(record.start_time), 'seconds')
        };
      });

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

  /**
   * Get attendance by employee ID
   */
  async getAttendanceById(req, res) {
    try {
      const { id } = req.params;
      let { start_date, end_date, skip = 0, limit = 10 } = req.body;
      
      start_date = moment(start_date).format("YYYY-MM-DD");
      end_date = moment(end_date).format("YYYY-MM-DD");

      // Run both queries in parallel
      const [attendanceRecords, attendanceRecordCount] = await Promise.all([
        AdminModel.getAllAttendanceById(id, start_date, end_date, skip, limit),
        AdminModel.getAttendanceCount(start_date, end_date, id)
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

  /**
   * Get web app activity
   */
  async getWebAppActivity(req, res) {
    try {
      // Support both GET (query params) and POST (body params)
      const params = req.method === 'GET' ? req.query : req.body;
      let { employeeId, startDate, endDate, type = 1 } = params;
      
      startDate = moment(startDate).format('YYYY-MM-DD');
      
      if (moment(startDate).isSame(endDate)) {
        endDate = moment(endDate).endOf('day').format('YYYY-MM-DD');
      }
      
      if (!employeeId) {
        return res.status(400).json({ message: 'employeeId and startDate are required.' });
      }

      const activityRecords = await AdminModel.getWebAppActivityFiltered(
        parseInt(employeeId),
        startDate,
        endDate,
        +type
      );
      
      return res.json({ code: 200, data: activityRecords, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= DEPARTMENT MANAGEMENT =============
  
  /**
   * Get all departments
   */
  async getDepartments(req, res) {
    try {
      const departments = await AdminModel.getDepartments();
      return res.json({ code: 200, data: departments, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Add department
   */
  async addDepartment(req, res) {
    try {
      const { departmentName, locationId } = req.body;

      if (!departmentName || !locationId) {
        return res.status(400).json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
      }

      // Check if department already exists
      const existingDepartment = await AdminModel.getDepartmentByName(departmentName);
      if (existingDepartment) {
        return res.status(400).json({ code: 400, data: null, error: null, message: 'Department already exists' });
      }

      // Add department
      const departmentId = await AdminModel.addDepartment(departmentName, locationId);

      return res.json({ code: 200, data: { id: departmentId, departmentName, locationId }, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Update department
   */
  async updateDepartment(req, res) {
    try {
      const { id, departmentName, locationId } = req.body;
      
      if (!id || !departmentName) {
        return res.status(400).json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
      }
      
      const department = await AdminModel.getDepartmentById(id);
      if (!department) {
        return res.status(404).json({ code: 404, data: null, error: null, message: `Department with id ${id} not found` });
      }

      await AdminModel.updateDepartment(id, departmentName, locationId);
      return res.json({ code: 200, data: { id, departmentName, locationId }, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Delete department
   */
  async deleteDepartment(req, res) {
    try {
      const { id } = req.params;
      
      if (!id) {
        return res.status(400).json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
      }
      
      const department = await AdminModel.getDepartmentById(id);
      if (!department) {
        return res.status(404).json({ code: 404, data: null, error: null, message: `Department with id ${id} not found` });
      }
      
      const isDepartmentUsed = await AdminModel.isDepartmentUsed(id);
      if (isDepartmentUsed) {
        return res.status(400).json({ code: 400, data: null, error: null, message: 'Department is being used by an employee' });
      }
      
      await AdminModel.deleteDepartment(id);
      return res.json({ code: 200, data: null, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= LOCATION MANAGEMENT =============
  
  /**
   * Get all locations
   */
  async getLocations(_req, res) {
    try {
      const locations = await AdminModel.getLocations();
      return res.json({ code: 200, data: locations, error: null, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Add location
   */
  async addLocation(req, res) {
    try {
      const { locationName } = req.body;
      const trimmedName = locationName?.trim();
      
      if (!trimmedName) {
        return res.status(400).json({ code: 400, message: 'Invalid inputs' });
      }

      const existingLocation = await AdminModel.getLocationByName(trimmedName);
      if (existingLocation?.length) {
        return res.status(400).json({ code: 400, message: 'Location already exists' });
      }

      const locationId = await AdminModel.addLocation(trimmedName);
      return res.json({ code: 200, data: { id: locationId, locationName: trimmedName }, message: 'Success' });

    } catch (error) {
      console.error("Add Location Error:", error.message);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Update location
   */
  async updateLocation(req, res) {
    try {
      const { id: locationId } = req.params;
      const { locationName } = req.body;
      
      if (!locationName) {
        return res.status(400).json({ code: 400, message: 'Location name is required' });
      }

      const location = await AdminModel.getLocationById(locationId);
      if (!location?.length) {
        return res.status(404).json({ code: 404, message: `Location with id ${locationId} not found` });
      }

      await AdminModel.updateLocation(locationId, locationName);
      return res.json({ code: 200, data: { id: locationId, locationName }, message: 'Location updated successfully' });

    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Delete location
   */
  async deleteLocation(req, res) {
    try {
      const { id } = req.params;
      
      if (!id) {
        return res.status(400).json({ code: 400, message: 'Location ID is required' });
      }

      const location = await AdminModel.getLocationById(id);
      if (!location?.length) {
        return res.status(404).json({ code: 404, message: `Location with ID ${id} not found` });
      }

      const hasDepartments = await AdminModel.hasDepartmentsInLocation(id);
      if (hasDepartments) {
        return res.status(400).json({ code: 400, message: 'Location has departments and cannot be deleted' });
      }

      await AdminModel.deleteLocation(id);
      return res.json({ code: 200, message: 'Location deleted successfully' });

    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= REPORTS =============
  
  /**
   * Get reports
   */
  async getReports(req, res) {
    try {
      let { id: organization_id } = req.user;
      
      // Support both GET (query params) and POST (body params)
      const params = req.method === 'GET' ? req.query : req.body;
      let { employee_id, department_id, location_id, start_date, end_date, skip, limit } = params;
      
      start_date = moment(start_date).format("YYYY-MM-DD");
      end_date = moment(end_date).add(1, 'day').format("YYYY-MM-DD");
      
      let [employeeData] = await AdminModel.filterEmployee({ employee_id, department_id, location_id, organization_id });
      let emp_ids = _.pluck(employeeData, 'id');
      
      let [data, count] = await Promise.all([
        AdminModel.getReports({ employee_id: emp_ids, start_date, end_date }),
        AdminModel.getReportsCount({ employee_id: emp_ids, start_date, end_date }),
      ]);
      
      data = data.map(i => {
        let emp = employeeData.find(x => x.id == i.employee_id);
        return {
          ...i,
          ...emp
        };
      });
      
      return res.status(200).json({
        code: 200,
        data: { data, count },
        error: null,
        message: "Success"
      });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= LOCALIZATION =============
  
  /**
   * Update localization data
   */
  async updateLocalizationData(req, res) {
    try {
      let { id: organization_id } = req.user;
      let { language, timezone } = req.body;
      
      if (!language || !timezone) {
        return res.status(400).json({ message: 'Invalid inputs' });
      }
      
      await AdminModel.upsertLocalizationData({ organization_id, lang: language, timezone });
      return res.status(200).json({ message: 'Localization data updated successfully' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Get localization data
   */
  async getLocalizationData(req, res) {
    try {
      let { id: organization_id } = req.user;
      let data = await AdminModel.getLocalizationData(organization_id);
      return res.status(200).json({ code: 200, data, message: 'Success' });
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  // ============= PRODUCTIVITY RULES =============
  
  /**
   * Get productivity rules
   */
  async getProductivityRules(req, res) {
    try {
      let { id: organization_id } = req.user;
      let { skip = 0, limit = 10, search = "" } = req.query;
      
      if (skip) skip = parseInt(skip) || 0;
      if (limit) limit = parseInt(limit) || 10;
      
      let [data, count] = await Promise.all([
        AdminModel.getProductivityRules({ skip, limit, search, organization_id }),
        AdminModel.getProductivityRulesCount({ search, organization_id })
      ]);
      
      return res.status(200).json({ code: 200, data: { data, count }, message: 'Success' });
    }
    catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Update productivity rules
   */
  async updateProductivityRules(req, res) {
    try {
      let { _id, category } = req.body;
      let prRules = await AdminModel.getProductivityById(_id);
      
      if (!prRules) {
        return res.status(404).json({ message: 'Productivity rule not found' });
      }
      
      prRules.category = category || prRules.category;
      await prRules.save();
      
      return res.status(200).json({ code: 200, data: prRules, message: 'Success' });
    }
    catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }
}

module.exports = new AdminController();
