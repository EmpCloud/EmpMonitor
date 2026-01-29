'use strict';

const router = require('express').Router();
const EmployeeController = require('./employee.controller');
const authMiddleware = require('../../middleware/authMiddleware');
const { authLimiter, strictLimiter } = require('../../middleware/rateLimitMiddleware');

class EmployeeRoutes {
  constructor() {
    this.myRoutes = router;
    this.core();
  }

  core() {
    // Public routes - with strict rate limiting to prevent brute force attacks
    this.myRoutes.post('/login', authLimiter, (req, res) => EmployeeController.employeeLogin(req, res));
    
    // Protected routes (require authentication)
    this.myRoutes.use(authMiddleware.authenticateToken);
    this.myRoutes.use(authMiddleware.authorizeRole(['employee']));
    
    // Apply rate limiting to all protected routes
    this.myRoutes.use(strictLimiter);
    
    // Employee operations
    this.myRoutes.get('/', (req, res) => EmployeeController.getEmployee(req, res));
    this.myRoutes.put('/', (req, res) => EmployeeController.updateEmployee(req, res));
    this.myRoutes.get('/employees/:id', (req, res) => EmployeeController.getEmployeeById(req, res));
    
    // Attendance
    this.myRoutes.post('/attendance', (req, res) => EmployeeController.getAttendance(req, res));
    
    // Web app activity - POST only to avoid sensitive data in query params
    this.myRoutes.post('/web-app-activity', (req, res) => EmployeeController.getWebAppActivity(req, res));
  }

  getRouters() {
    return this.myRoutes;
  }
}

module.exports = EmployeeRoutes;
