'use strict';

const router = require('express').Router();
const EmployeeController = require('./employee.controller');
const authMiddleware = require('../../middleware/authMiddleware');

class EmployeeRoutes {
  constructor() {
    this.myRoutes = router;
    this.core();
  }

  core() {
    // Public routes
    this.myRoutes.post('/login', (req, res) => EmployeeController.employeeLogin(req, res));
    
    // Protected routes (require authentication)
    this.myRoutes.use(authMiddleware.authenticateToken);
    this.myRoutes.use(authMiddleware.authorizeRole(['employee']));
    
    // Employee operations
    this.myRoutes.get('/', (req, res) => EmployeeController.getEmployee(req, res));
    this.myRoutes.put('/', (req, res) => EmployeeController.updateEmployee(req, res));
    this.myRoutes.get('/employees/:id', (req, res) => EmployeeController.getEmployeeById(req, res));
    
    // Attendance
    this.myRoutes.post('/attendance', (req, res) => EmployeeController.getAttendance(req, res));
    
    // Web app activity
    this.myRoutes.get('/web-app-activity', (req, res) => EmployeeController.getWebAppActivity(req, res));
    this.myRoutes.post('/web-app-activity', (req, res) => EmployeeController.getWebAppActivity(req, res));
  }

  getRouters() {
    return this.myRoutes;
  }
}

module.exports = EmployeeRoutes;
