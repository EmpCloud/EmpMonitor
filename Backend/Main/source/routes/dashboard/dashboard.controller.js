const DashboardModel = require('./dashboard.model');
const moment = require('moment-timezone');
const _ = require('underscore');
require('dotenv').config();

class DashboardController {
  /**
   * Get employee statistics (total, present, absent)
   */
  async getEmployeeStats(req, res) {
    try {
      const { date } = req.query;
      const targetDate = date ? moment(date).format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
      
      const [totalEmployees, presentEmployees, absentEmployees] = await Promise.all([
        DashboardModel.getTotalEmployees(),
        DashboardModel.getPresentEmployees(targetDate),
        DashboardModel.getAbsentEmployees(targetDate)
      ]);

      return res.json({
        code: 200,
        data: {
          totalEmployees,
          presentEmployees,
          absentEmployees
        },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error in getEmployeeStats:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Get top application usage
   */
  async getTopApplications(req, res) {
    try {
      const { period = 'today', limit = 10 } = req.query;
      
      const { startDate, endDate } = this.getDateRange(period);
      
      // Get attendance IDs for the date range
      const attendanceRecords = await DashboardModel.getAllAttendance(startDate, endDate);
      const attendanceIds = _.pluck(attendanceRecords, 'attendance_id');
      
      const topApplications = await DashboardModel.getTopApplications(
        startDate,
        endDate,
        parseInt(limit),
        attendanceIds
      );

      return res.json({
        code: 200,
        data: topApplications,
        message: 'Success'
      });
    } catch (error) {
      console.error('Error in getTopApplications:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Get top website usage
   */
  async getTopWebsites(req, res) {
    try {
      const { period = 'today', limit = 10 } = req.query;
      
      const { startDate, endDate } = this.getDateRange(period);
      
      // Get attendance IDs for the date range
      const attendanceRecords = await DashboardModel.getAllAttendance(startDate, endDate);
      const attendanceIds = _.pluck(attendanceRecords, 'attendance_id');
      
      const topWebsites = await DashboardModel.getTopWebsites(
        startDate,
        endDate,
        parseInt(limit),
        attendanceIds
      );

      return res.json({
        code: 200,
        data: topWebsites,
        message: 'Success'
      });
    } catch (error) {
      console.error('Error in getTopWebsites:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Get employees by active hours (most and least)
   */
  async getEmployeesByActiveHours(req, res) {
    try {
      const { period = 'today', limit = 5 } = req.query;
      
      const { startDate, endDate } = this.getDateRange(period);
      
      // Get attendance IDs for the date range
      const attendanceRecords = await DashboardModel.getAllAttendance(startDate, endDate);
      const attendanceIds = _.pluck(attendanceRecords, 'attendance_id');
      
      const [mostActive, leastActive] = await Promise.all([
        DashboardModel.getEmployeesByActiveHours(startDate, endDate, parseInt(limit), 'desc', attendanceIds),
        DashboardModel.getEmployeesByActiveHours(startDate, endDate, parseInt(limit), 'asc', attendanceIds)
      ]);

      return res.json({
        code: 200,
        data: {
          mostActive,
          leastActive
        },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error in getEmployeesByActiveHours:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Get employees by productive hours (most and least)
   */
  async getEmployeesByProductiveHours(req, res) {
    try {
      const { period = 'today', limit = 5 } = req.query;
      
      const { startDate, endDate } = this.getDateRange(period);
      
      // Get attendance IDs for the date range
      const attendanceRecords = await DashboardModel.getAllAttendance(startDate, endDate);
      const attendanceIds = _.pluck(attendanceRecords, 'attendance_id');
      
      const [mostProductive, leastProductive] = await Promise.all([
        DashboardModel.getEmployeesByProductiveHours(startDate, endDate, parseInt(limit), 'desc', attendanceIds),
        DashboardModel.getEmployeesByProductiveHours(startDate, endDate, parseInt(limit), 'asc', attendanceIds)
      ]);

      return res.json({
        code: 200,
        data: {
          mostProductive,
          leastProductive
        },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error in getEmployeesByProductiveHours:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }

  /**
   * Helper function to get date range based on period
   */
  getDateRange(period) {
    let startDate, endDate;
    
    switch (period) {
      case 'today':
        startDate = moment().startOf('day').format('YYYY-MM-DD');
        endDate = moment().endOf('day').format('YYYY-MM-DD');
        break;
      case 'yesterday':
        startDate = moment().subtract(1, 'day').startOf('day').format('YYYY-MM-DD');
        endDate = moment().subtract(1, 'day').endOf('day').format('YYYY-MM-DD');
        break;
      case 'week':
        startDate = moment().startOf('week').format('YYYY-MM-DD');
        endDate = moment().endOf('week').format('YYYY-MM-DD');
        break;
      default:
        startDate = moment().startOf('day').format('YYYY-MM-DD');
        endDate = moment().endOf('day').format('YYYY-MM-DD');
    }
    
    return { startDate, endDate };
  }

  /**
   * Get all dashboard stats in one call
   */
  async getAllDashboardStats(req, res) {
    try {
      const { period = 'today', limit = 10 } = req.query;
      const targetDate = moment().format('YYYY-MM-DD');
      
      const { startDate, endDate } = this.getDateRange(period);
      
      // Get attendance IDs for the date range
      const attendanceRecords = await DashboardModel.getAllAttendance(startDate, endDate);
      const attendanceIds = _.pluck(attendanceRecords, 'attendance_id');
      
      const [
        totalEmployees,
        presentEmployees,
        absentEmployees,
        topApplications,
        topWebsites,
        mostActive,
        leastActive,
        mostProductive,
        leastProductive
      ] = await Promise.all([
        DashboardModel.getTotalEmployees(),
        DashboardModel.getPresentEmployees(targetDate),
        DashboardModel.getAbsentEmployees(targetDate),
        DashboardModel.getTopApplications(startDate, endDate, parseInt(limit), attendanceIds),
        DashboardModel.getTopWebsites(startDate, endDate, parseInt(limit), attendanceIds),
        DashboardModel.getEmployeesByActiveHours(startDate, endDate, 5, 'desc', attendanceIds),
        DashboardModel.getEmployeesByActiveHours(startDate, endDate, 5, 'asc', attendanceIds),
        DashboardModel.getEmployeesByProductiveHours(startDate, endDate, 5, 'desc', attendanceIds),
        DashboardModel.getEmployeesByProductiveHours(startDate, endDate, 5, 'asc', attendanceIds)
      ]);

      return res.json({
        code: 200,
        data: {
          employeeStats: {
            totalEmployees,
            presentEmployees,
            absentEmployees
          },
          topApplications,
          topWebsites,
          activeHours: {
            mostActive,
            leastActive
          },
          productiveHours: {
            mostProductive,
            leastProductive
          }
        },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error in getAllDashboardStats:', error);
      return res.status(500).json({ message: 'Internal server error', error: error.message });
    }
  }
}

module.exports = new DashboardController();

