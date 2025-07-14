const AdminModel = require('./admin.model');
const EmployeeModel = require('../employee/employee.model');
const jwt = require('jsonwebtoken');
require('dotenv').config();

const moment = require('moment-timezone');
const _ = require('underscore');

const DEFAULT_ADMIN_EMAIL = process.env.DEFAULT_ADMIN_EMAIL || 'admin@example.com';
const DEFAULT_ADMIN_PASSWORD = process.env.DEFAULT_ADMIN_PASSWORD || 'password123';
class AdminController{

async adminLogin(req, res) {
  try {
    const { email, password } = req.body;
  if (email === DEFAULT_ADMIN_EMAIL && password === DEFAULT_ADMIN_PASSWORD) {
      const token = AdminModel.generateToken({ id: 1, role: 'admin', license: 5 }); // Using ID 1 as per your sample, ensure it's not a real DB ID
      return res.json({ token, role: 'admin', license: 5 });
    } else {
      const admin = await AdminModel.getAdminByEmail(email);
      if (admin && AdminModel.decryptPassword(password, admin.password) && admin.role === 'admin') {
        const token = AdminModel.generateToken({ id: admin.id, role: admin.role, license: admin.license });
        const adminResponse = { ...admin };
        delete adminResponse.password;
        return res.json({ token, ...adminResponse });
      } else {
  return res.status(401).send('Invalid credentials');
      }
    }
  } catch (error) {
      console.error('Error during admin login:', error);
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async adminRegister(req, res) {
    try {
    if (!req.user || req.user.role !== 'admin') {
      return res.status(403).json({ message: 'Forbidden: Admin role required for this action.' });
        }
        const { firstName, lastName, email, password, mobileNumber, employeeCode, timeZone, departmentId, locationId } = req.body;
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            return res.status(400).json({ error: 'Invalid email format.' });
        }
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
        const departmentExists = await EmployeeModel.getDepartmentById(departmentId);
        if (!departmentExists) {
            return res.status(404).json({ error: 'Department not found.' });
        }

        const locationExists = await EmployeeModel.getLocationById(locationId);
        if (!locationExists) {
            return res.status(404).json({ error: 'Location not found.' });
        }

        const adminId = req.user.id;
        const adminLicenseLimit = await AdminModel.getAdminLicenseCount(adminId);
        const currentEmployeeCount = await EmployeeModel.countEmployees();


        if (adminLicenseLimit !== null && currentEmployeeCount >= adminLicenseLimit) {
            return res.status(403).json({ message: `License limit of ${adminLicenseLimit} employees reached. Cannot register more employees.` });
        }
        const hashedPassword = AdminModel.encryptPassword(password);

        const employeeId = await EmployeeModel.registerEmployee({
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

        res.status(201).json({ message: 'Employee registered successfully!', id: employeeId,//       id: employeeId,
      firstName,
      lastName,
      email,
      mobileNumber,
      employeeCode,
      timeZone,
      role: 'employee',
      departmentId,
      locationId });
    } catch (error) {
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Email already registered.' });
        }
        console.error('Error during admin registration:', error);
        return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
}

async getAllEmployees(req, res) {
  try {
    let { skip = 0, limit = 10, name } = req.query;

    const [employees, count] = await Promise.all([
      EmployeeModel.getAllEmployees(+skip, +limit, name),
      EmployeeModel.getAllEmployees(+skip, +limit, name, 1)
    ]);
 
    return res.status(200).json({ employees, totalCount: count });
  } catch (err) {
    return res.status(500).json({ message: 'Internal server error', error: err.message });
  }
}

async deleteEmployee(req, res) {
  try {
    const { id } = req.params;
    if (!id) return res.status(400).json({ message: 'Invalid inputs' });
    let result= await EmployeeModel.getEmployeeById(id);
    if(!result) return res.status(404).json({ message: 'Employee not found' });
    await EmployeeModel.deleteEmployee(id);
    res.status(200).json({ message: 'Employee deleted successfully' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}


async deleteEmployees(req, res) {
  try {
    const { user_ids } = req.body;
    if (!user_ids || !Array.isArray(user_ids)) return res.status(400).json({ message: 'Invalid inputs' }  );
    let result = await Promise.all(user_ids.map(id => EmployeeModel.getEmployeeById(id)));
if(!result.length) return res.status(404).json({ message: 'Employee not found' });
    await Promise.all(user_ids.map(id => EmployeeModel.deleteEmployee(id)));
    res.status(200).json({ message: 'Employee deleted successfully' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}


async getEmployeeById(req, res) {
  try {
    const { id } = req.params;
    let resp = await EmployeeModel.getEmployeeById(id);
    let password = await AdminModel.decryptPassword(null, resp.password);
    resp.password = password;
    res.status(200).json({ message: 'Success', data: resp });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async getAttendance(req, res) {
  try {
    let { start_date, end_date, skip = 0, limit = 10, employee_id, name } = req.body;
    start_date = moment(start_date).format("YYYY-MM-DD");
    end_date = moment(end_date).format("YYYY-MM-DD");

    // Run both queries in parallel
    let [attendanceRecords, attendanceRecordCount] = await Promise.all([
      EmployeeModel.getAllAttendance(start_date, end_date, +skip, +limit, employee_id, name, 0),
      EmployeeModel.getAllAttendance(start_date, end_date, null, null, employee_id, name, 1)
    ]);

    let dates = _.pluck(attendanceRecords, 'date').map(i => +moment(i).format('YYYY-MM-DD').split("-").join(''));
    let timesheet = await EmployeeModel.getEmployeeTimesheet({ dates, employee_id});
    
    attendanceRecords = attendanceRecords.map(record => {
      let date = +moment(record.date).format('YYYY-MM-DD').split("-").join('');
      let timesheetRecord = timesheet.find(ts => ts.yyyymmdd === date && ts.employee_id === record.employee_id);
      return {
        ...record,
        ...timesheetRecord
      }
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


async getAttendanceById(req, res) {
  try {
    const { id } = req.params;
    let { start_date, end_date, skip = 0, limit = 10 } = req.body;
    
    start_date = moment(start_date).format("YYYY-MM-DD");
    end_date = moment(end_date).format("YYYY-MM-DD");

    // Run both queries in parallel
    const [attendanceRecords, attendanceRecordCount] = await Promise.all([
      EmployeeModel.getAllAttendanceById(id, start_date, end_date, skip, limit),
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

async getWebAppActivity(req, res) {
  try {
    let { employeeId, startDate, endDate, type = 1 } = req.query;
    startDate = moment(startDate).format('YYYY-MM-DD');
    if(moment(startDate).isSame(endDate)) endDate = moment(endDate).endOf('day').format('YYYY-MM-DD');
    if (!employeeId) {
      return res.status(400).json({ message: 'employeeId and startDate are required.' });
    }

    const activityRecords = await EmployeeModel.getWebAppActivityFiltered(
      parseInt(employeeId),
      startDate,
      endDate,
      +type
    );
    return res.json({code: 200, data: activityRecords, error: null, message: 'Success'});
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

  async updateEmployee(req, res) { 
    try {
       const {id, role} = await AdminModel.getLoginUserData(req);
       if(role !== 'admin') return res.status(400).json({ message: 'Admin can update employee', error: null });

       const {employeeId, firstName, lastName, employeeRole, mobileNumber, employeeCode, timeZone, password, email} = req.body;
       if(!(employeeId && firstName && lastName && employeeRole && email && password && mobileNumber && employeeCode && timeZone && email)) return res.json({code: 400, data: null, error: null, message: 'Invalid inputs'});

       const hashedPassword = await AdminModel.encryptPassword(password);
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

       await EmployeeModel.updateEmployee(employeeId, employeeData);
      employeeData.employeeId = employeeId;
      return res.json({code: 200, data: employeeData, error: null, message: 'Success'});
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
}


async getDepartments(req, res) {
  try {
    const departments = await EmployeeModel.getDepartments();
    return res.json({ code: 200, data: departments, error: null, message: 'Success' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async addDepartment(req, res) {
  try {
    const { departmentName, locationId } = req.body;

    if (!departmentName || !locationId) {
      return res.status(400).json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
    }

    // Check if department already exists
    const existingDepartment = await EmployeeModel.getDepartmentByName(departmentName);
    if (existingDepartment) {
      return res.status(400).json({ code: 400, data: null, error: null, message: 'Department already exists' });
    }

    // Add department
    const departmentId = await EmployeeModel.addDepartment(departmentName, locationId);

    return res.json({ code: 200, data: { id: departmentId, departmentName, locationId }, error: null, message: 'Success' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async updateDepartment(req, res) {
  try {
    const { id, departmentName } = req.body;
    if (!id || !departmentName) return res.status(400).json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
    const department = await EmployeeModel.getDepartmentById(id);
    if (!department)
      return res.status(404).json({ code: 404, data: null, error: null, message: `Department with id ${id} not found` });

    await EmployeeModel.updateDepartment(id, departmentName);
    return res.json({ code: 200, data: { id, departmentName }, error: null, message: 'Success' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async deleteDepartment(req, res) {
  try {
    const { id } = req.params;
    if (!id) return res.status(400).json({ code: 400, data: null, error: null, message: 'Invalid inputs' });
    const department = await EmployeeModel.getDepartmentById(id);
    if (!department) return res.status(404).json({ code: 404, data: null, error: null, message: `Department with id ${id} not found` });
    const isDepartmentUsed = await EmployeeModel.isDepartmentUsed(id);
    if (isDepartmentUsed) return res.status(400).json({ code: 400, data: null, error: null, message: 'Department is being used by an employee' });
    await EmployeeModel.deleteDepartment(id);
    return res.json({ code: 200, data: null, error: null, message: 'Success' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async getLocations(_req, res) {
  try {
    const locations = await EmployeeModel.getLocations();
    return res.json({ code: 200, data: locations, error: null, message: 'Success' });
  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async addLocation(req, res) {
  try {
    const { locationName } = req.body;
    const trimmedName = locationName?.trim();
    if (!trimmedName) return res.status(400).json({ code: 400, message: 'Invalid inputs' });

    const existingLocation = await EmployeeModel.getLocationByName(trimmedName);
    if (existingLocation?.length) return res.status(400).json({ code: 400, message: 'Location already exists' });

    const locationId = await EmployeeModel.addLocation(trimmedName);
    return res.json({ code: 200, data: { id: locationId, locationName: trimmedName }, message: 'Success' });

  } catch (error) {
    console.error("Add Location Error:", error.message);
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async updateLocation(req, res) {
  try {
    const { id: locationId } = req.params;
    const { locationName } = req.body;
    if (!locationName) return res.status(400).json({ code: 400, message: 'Location name is required' });

    const location = await EmployeeModel.getLocationById(locationId);
    if (!location?.length) return res.status(404).json({ code: 404, message: `Location with id ${locationId} not found` });

    await EmployeeModel.updateLocation(locationId, locationName);
    return res.json({ code: 200, data: { id: locationId, locationName }, message: 'Location updated successfully' });

  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

async deleteLocation(req, res) {
  try {
    const { id } = req.params;
    if (!id) return res.status(400).json({ code: 400, message: 'Location ID is required' });

    const location = await EmployeeModel.getLocationById(id);
    if (!location?.length) return res.status(404).json({ code: 404, message: `Location with ID ${id} not found` });

    const hasDepartments = await EmployeeModel.hasDepartmentsInLocation(id);
    if (hasDepartments) return res.status(400).json({ code: 400, message: 'Location has departments and cannot be deleted' });

    await EmployeeModel.deleteLocation(id);
    return res.json({ code: 200, message: 'Location deleted successfully' });

  } catch (error) {
    return res.status(500).json({ message: 'Internal server error', error: error.message });
  }
}

  async getReports(req, res) {
    try {
      let { id: organization_id } = req.user;
      let { employee_id, department_id, location_id, start_date, end_date } = req.query;
      start_date = moment(start_date).format("YYYY-MM-DD");
      end_date = moment(end_date).add(1, 'day').format("YYYY-MM-DD");
      let [employeeData] = await EmployeeModel.filterEmployee({ employee_id, department_id, location_id });
      let emp_ids = _.pluck(employeeData, 'id');
      let [data, count] = await Promise.all([
        EmployeeModel.getReports({ employee_id: emp_ids, start_date, end_date }),
        EmployeeModel.getReportsCount({ employee_id: emp_ids, start_date, end_date  }),
      ]);
      data = data.map(i => {
        let emp = employeeData.find(x => x.id == i.employee_id);
        return {
          ...i,
          ...emp
        }
      })
      return res.status(200).json({
        code: 200,
        data: { data, count },
        error: null,
        message: "Success"
      })
    } catch (error) {
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

}
module.exports = new AdminController();