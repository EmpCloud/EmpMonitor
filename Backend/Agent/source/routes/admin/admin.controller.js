const authModel = require('./admin.model');
const responseHandler = require('../../utils/helper/responseHandler');
require('dotenv').config();

class AdminController {
    async adminLogin(req, res) {
        try {
            const { email, password } = req.body;
            if (!email || !password) {
                return responseHandler(res, 400, null, 'Email and password are required');
            }
            const employee = await authModel.getEmployeeByEmail(email);
            if (!employee) {
                return responseHandler(res, 401, null, 'Invalid email or password');
            }

            const passwordMatch = await authModel.decryptPassword(password, employee.password);

            if (!passwordMatch) {
                return responseHandler(res, 401, null, 'Invalid email or password');
            }
            const token = authModel.generateToken({ id: employee.id, email: employee.email, role: employee.role });
            delete employee.password;
            return res.json({  
                "success": true,
                "accessToken": token,
                "identifier": employee.id,
                ...employee
            })
            // return responseHandler(res, 200, { token, ...employee }, 'Login successful');
        } catch (error) {
            console.error('Error during admin login:', error);
            return responseHandler(res, 500, null, 'Internal server error', error.message);
        }
    }
}

module.exports = new AdminController();
