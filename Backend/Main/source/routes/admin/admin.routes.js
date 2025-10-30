'use strict';

const router = require('express').Router();
const AdminController = require('./admin.controller');
const MonitoringControlController = require('./monitoring-control.controller');
const authMiddleware = require('../../middleware/authMiddleware');

class AdminRoutes {
  constructor() {
    this.myRoutes = router;
    this.core();
  }

  core() {
    // Public routes
    this.myRoutes.post('/login', (req, res) => AdminController.adminLogin(req, res));
    
    // Protected routes (require authentication)
    this.myRoutes.use(authMiddleware.authenticateToken);
    this.myRoutes.use(authMiddleware.authorizeRole(['admin']));
    
    // Employee management
    this.myRoutes.post('/register', (req, res) => AdminController.adminRegister(req, res));
    this.myRoutes.get('/employees', (req, res) => AdminController.getAllEmployees(req, res));
    this.myRoutes.get('/employees/:id', (req, res) => AdminController.getEmployeeById(req, res));
    this.myRoutes.put('/update-employee', (req, res) => AdminController.updateEmployee(req, res));
    this.myRoutes.delete('/employees/:id', (req, res) => AdminController.deleteEmployee(req, res));
    this.myRoutes.delete('/employee-delete-multiple', (req, res) => AdminController.deleteEmployees(req, res));
    
    // Attendance
    this.myRoutes.post('/attendance', (req, res) => AdminController.getAttendance(req, res));
    this.myRoutes.post('/attendance/:id', (req, res) => AdminController.getAttendanceById(req, res));
    
    // Web app activity
    this.myRoutes.get('/web-app-activity', (req, res) => AdminController.getWebAppActivity(req, res));
    this.myRoutes.post('/web-app-activity', (req, res) => AdminController.getWebAppActivity(req, res));
    
    // Department management
    this.myRoutes.get('/get-departments', (req, res) => AdminController.getDepartments(req, res));
    this.myRoutes.post('/add-department', (req, res) => AdminController.addDepartment(req, res));
    this.myRoutes.put('/update-department', (req, res) => AdminController.updateDepartment(req, res));
    this.myRoutes.delete('/delete-department/:id', (req, res) => AdminController.deleteDepartment(req, res));
    
    // Location management
    this.myRoutes.get('/locations', (req, res) => AdminController.getLocations(req, res));
    this.myRoutes.post('/locations', (req, res) => AdminController.addLocation(req, res));
    this.myRoutes.put('/locations/:id', (req, res) => AdminController.updateLocation(req, res));
    this.myRoutes.delete('/locations/:id', (req, res) => AdminController.deleteLocation(req, res));
    
    // Reports
    this.myRoutes.get('/report', (req, res) => AdminController.getReports(req, res));
    this.myRoutes.post('/report', (req, res) => AdminController.getReports(req, res));
    
    // Localization
    this.myRoutes.post('/localization', (req, res) => AdminController.updateLocalizationData(req, res));
    this.myRoutes.get('/localization', (req, res) => AdminController.getLocalizationData(req, res));
    
    // Productivity rules
    this.myRoutes.get('/productivity-rules', (req, res) => AdminController.getProductivityRules(req, res));
    this.myRoutes.put('/productivity-rules', (req, res) => AdminController.updateProductivityRules(req, res));
    
    // Monitoring Control - Rules Management
    this.myRoutes.get('/monitoring-rules', (req, res) => MonitoringControlController.getAllRules(req, res));
    this.myRoutes.get('/monitoring-rules/:id', (req, res) => MonitoringControlController.getRuleById(req, res));
    this.myRoutes.post('/monitoring-rules', (req, res) => MonitoringControlController.createRule(req, res));
    this.myRoutes.put('/monitoring-rules/:id', (req, res) => MonitoringControlController.updateRule(req, res));
    this.myRoutes.delete('/monitoring-rules/:id', (req, res) => MonitoringControlController.deleteRule(req, res));
    
    // Monitoring Control - Employee Assignment
    this.myRoutes.get('/monitoring-rules/:id/employees', (req, res) => MonitoringControlController.getEmployeesByRule(req, res));
    this.myRoutes.post('/monitoring-rules/:id/employees', (req, res) => MonitoringControlController.assignEmployeesToRule(req, res));
    this.myRoutes.post('/monitoring-rules/assign-employee', (req, res) => MonitoringControlController.assignEmployeeToRule(req, res));
    this.myRoutes.get('/monitoring-rules/unassigned/employees', (req, res) => MonitoringControlController.getUnassignedEmployees(req, res));
    this.myRoutes.post('/monitoring-rules/assign-unassigned', (req, res) => MonitoringControlController.assignUnassignedToDefault(req, res));
    this.myRoutes.get('/employees/:employeeId/monitoring-rule', (req, res) => MonitoringControlController.getEmployeeRule(req, res));
  }

  getRouters() {
    return this.myRoutes;
  }
}

module.exports = AdminRoutes;
