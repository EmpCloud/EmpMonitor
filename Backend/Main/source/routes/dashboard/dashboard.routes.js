const express = require('express');
const DashboardController = require('./dashboard.controller');
const authMiddleware = require('../../middleware/authMiddleware');

class DashboardRoutes {
  constructor() {
    this.myRoutes = express.Router();
    this.initializeRoutes();
  }

  initializeRoutes() {
    // Apply authentication middleware to all routes
    this.myRoutes.use(authMiddleware.authenticateToken);
    
    // Employee statistics
    this.myRoutes.get('/employee-stats', (req, res) => 
      DashboardController.getEmployeeStats(req, res)
    );

    // Top applications
    this.myRoutes.get('/top-applications', (req, res) => 
      DashboardController.getTopApplications(req, res)
    );

    // Top websites
    this.myRoutes.get('/top-websites', (req, res) => 
      DashboardController.getTopWebsites(req, res)
    );

    // Employees by active hours
    this.myRoutes.get('/employees-active-hours', (req, res) => 
      DashboardController.getEmployeesByActiveHours(req, res)
    );

    // Employees by productive hours
    this.myRoutes.get('/employees-productive-hours', (req, res) => 
      DashboardController.getEmployeesByProductiveHours(req, res)
    );

    // Get all dashboard stats
    this.myRoutes.get('/all-stats', (req, res) => 
      DashboardController.getAllDashboardStats(req, res)
    );
  }

  getRouters() {
    return this.myRoutes;
  }
}

module.exports = DashboardRoutes;

