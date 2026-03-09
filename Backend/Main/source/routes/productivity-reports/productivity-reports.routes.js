const express = require('express');
const ProductivityReportsController = require('./productivity-reports.controller');
const authMiddleware = require('../../middleware/authMiddleware');
const { dashboardLimiter } = require('../../middleware/rateLimitMiddleware');

class ProductivityReportsRoutes {
  constructor() {
    this.myRoutes = express.Router();
    this.initializeRoutes();
  }

  initializeRoutes() {
    // Apply rate limiting
    this.myRoutes.use(dashboardLimiter);
    
    // Apply authentication middleware to all routes
    this.myRoutes.use(authMiddleware.authenticateToken);
    
    // Productivity reports routes
    this.myRoutes.get('/productivity-new', (req, res, next) => 
      ProductivityReportsController.getProductivityNew(req, res, next)
    );

    this.myRoutes.get('/productivity-list-new', (req, res, next) => 
      ProductivityReportsController.getProductivityListNew(req, res, next)
    );
  }

  getRouters() {
    return this.myRoutes;
  }
}

module.exports = ProductivityReportsRoutes;
