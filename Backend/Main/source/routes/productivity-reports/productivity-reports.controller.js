const ProductivityReportsModel = require('./productivity-reports.model');
const _ = require('underscore');

class ProductivityReportsController {
  /**
   * GET /productivity-new
   * Get productivity data with hierarchical filtering
   */
  async getProductivityNew(req, res, next) {
    try {
      // Extract authentication data from decoded JWT token
      let { id: organization_id, productive_hours, employee_id: Manager_ID } = req.user;
      
      // Extract query parameters
      let { 
        startDate, 
        endDate, 
        employee_id, 
        location_id = "All", 
        department_id, 
        skip = 0, 
        limit = 20 
      } = req.query;

      // Convert to numbers
      skip = parseInt(skip);
      limit = parseInt(limit);
      if (employee_id) employee_id = parseInt(employee_id);
      if (department_id) department_id = parseInt(department_id);
      if (location_id !== "All" && location_id !== "all") {
        location_id = parseInt(location_id);
      }

      // Manager employee filtering
      let specificEmployeeId = null;
      if (Manager_ID) {
        let assigned_employees_id = await ProductivityReportsModel.getEmployeeAssignedToManager(Manager_ID);
        specificEmployeeId = _.pluck(assigned_employees_id, 'employee_id');
        if (specificEmployeeId.length === 0) {
          // Manager has no assigned employees, return empty result
          return res.json({
            code: 200,
            total: 0,
            data: [],
            message: 'Productivity List.',
            error: null
          });
        }
      }

      // Fetch productivity data in parallel
      let [result, resultCount] = await Promise.all([
        ProductivityReportsModel.getEmployeeProductivityData(
          organization_id,
          location_id,
          department_id,
          employee_id,
          startDate,
          endDate,
          skip,
          limit,
          specificEmployeeId,
          Manager_ID
        ),
        ProductivityReportsModel.getEmployeeProductivityDataCount(
          organization_id,
          location_id,
          department_id,
          employee_id,
          startDate,
          endDate,
          specificEmployeeId,
          Manager_ID
        )
      ]);

      // Fetch additional details based on grouping
      let detailsData = [];
      let _location_id = [];
      let _department_id = [];
      let _employee_id = [];
      let _employee_d = [];

      if (location_id == "All" || location_id == "all") {
        _location_id = _.pluck(result, "_id");
        if (_location_id.length) {
          detailsData = await ProductivityReportsModel.getLocationData(_location_id, organization_id);
        }
      } else if (location_id !== "All" && location_id !== "all" && (!department_id && !employee_id)) {
        _department_id = _.pluck(result, "_id");
        if (_department_id.length) {
          detailsData = await ProductivityReportsModel.getDepartmentData(_department_id, organization_id);
        }
      } else if (location_id !== "All" && location_id !== "all" && (department_id && !employee_id)) {
        _employee_id = _.pluck(result, "_id");
        if (_employee_id.length) {
          detailsData = await ProductivityReportsModel.getEmployeeData(_employee_id, organization_id);
        }
      } else if (location_id !== "All" && location_id !== "all" && (department_id && employee_id)) {
        _employee_d = _.pluck(result, "employee_id");
        if (_employee_d.length) {
          detailsData = await ProductivityReportsModel.getEmployeeData(_employee_d, organization_id);
        }
      }

      // Enrich result data
      for (const item of result) {
        if (_employee_d.length == 0) {
          item.name = detailsData.filter(i => i.id == item._id)[0]?.name;
          item.computer_name = null;
          item.username = null;
        } else {
          let temp = detailsData.filter(i => i.id == item.employee_id)[0];
          item.full_name = temp?.name;
          item.computer_name = temp?.computer_name;
          item.username = temp?.username;
          item.name = item?.date;
        }
        
        // Calculate productivity percentages
        if (item.count && productive_hours) {
          item.productivity = ((item.productive_duration / productive_hours * 100) / item.count).toFixed(2);
          item.unproductivity = ((item.non_productive_duration / productive_hours * 100) / item.count).toFixed(2);
        } else {
          item.productivity = "0.00";
          item.unproductivity = "0.00";
        }
      }

      return res.json({
        code: 200,
        total: resultCount[0]?.myCount ?? 0,
        data: result,
        message: 'Productivity List.',
        error: null
      });
    } catch (err) {
      console.log(err);
      next(err);
    }
  }

  /**
   * GET /productivity-list-new
   * Get paginated productivity list with hierarchical filtering (default limit: 10)
   */
  async getProductivityListNew(req, res, next) {
    try {
      // Extract authentication data from decoded JWT token
      let { id: organization_id, productive_hours, employee_id: Manager_ID } = req.user;
      
      // Extract query parameters (default limit is 10 instead of 20)
      let { 
        startDate, 
        endDate, 
        employee_id, 
        location_id = "All", 
        department_id, 
        skip = 0, 
        limit = 10 
      } = req.query;

      // Convert to numbers
      skip = parseInt(skip);
      limit = parseInt(limit);
      if (employee_id) employee_id = parseInt(employee_id);
      if (department_id) department_id = parseInt(department_id);
      if (location_id !== "All" && location_id !== "all") {
        location_id = parseInt(location_id);
      }

      // Manager employee filtering
      let specificEmployeeId = null;
      if (Manager_ID) {
        let assigned_employees_id = await ProductivityReportsModel.getEmployeeAssignedToManager(Manager_ID);
        specificEmployeeId = _.pluck(assigned_employees_id, 'employee_id');
        if (specificEmployeeId.length === 0) {
          return res.json({
            code: 200,
            total: 0,
            data: [],
            message: 'Productivity List.',
            error: null
          });
        }
      }

      // Fetch productivity data in parallel
      let [result, resultCount] = await Promise.all([
        ProductivityReportsModel.getEmployeeProductivityData(
          organization_id,
          location_id,
          department_id,
          employee_id,
          startDate,
          endDate,
          skip,
          limit,
          specificEmployeeId,
          Manager_ID
        ),
        ProductivityReportsModel.getEmployeeProductivityDataCount(
          organization_id,
          location_id,
          department_id,
          employee_id,
          startDate,
          endDate,
          specificEmployeeId,
          Manager_ID
        )
      ]);

      // Fetch additional details based on grouping
      let detailsData = [];
      let _location_id = [];
      let _department_id = [];
      let _employee_id = [];
      let _employee_d = [];

      if (location_id == "All" || location_id == "all") {
        _location_id = _.pluck(result, "_id");
        if (_location_id.length) {
          detailsData = await ProductivityReportsModel.getLocationData(_location_id, organization_id);
        }
      } else if (location_id !== "All" && location_id !== "all" && (!department_id && !employee_id)) {
        _department_id = _.pluck(result, "_id");
        if (_department_id.length) {
          detailsData = await ProductivityReportsModel.getDepartmentData(_department_id, organization_id);
        }
      } else if (location_id !== "All" && location_id !== "all" && (department_id && !employee_id)) {
        _employee_id = _.pluck(result, "_id");
        if (_employee_id.length) {
          detailsData = await ProductivityReportsModel.getEmployeeData(_employee_id, organization_id);
        }
      } else if (location_id !== "All" && location_id !== "all" && (department_id && employee_id)) {
        _employee_d = _.pluck(result, "employee_id");
        if (_employee_d.length) {
          detailsData = await ProductivityReportsModel.getEmployeeData(_employee_d, organization_id);
        }
      }

      // Enrich result data (different from getProductivityNew - includes computer_name and username for all)
      for (const item of result) {
        if (_employee_d.length == 0) {
          let temp = detailsData.filter(i => i.id == item._id)[0];
          item.name = temp?.name;
          item.computer_name = temp?.computer_name;
          item.username = temp?.username;
        } else {
          let temp = detailsData.filter(i => i.id == item.employee_id)[0];
          item.full_name = temp?.name;
          item.computer_name = temp?.computer_name;
          item.username = temp?.username;
          item.name = item?.date;
        }
        
        // Calculate productivity percentages
        if (item.count && productive_hours) {
          item.productivity = ((item.productive_duration / productive_hours * 100) / item.count).toFixed(2);
          item.unproductivity = ((item.non_productive_duration / productive_hours * 100) / item.count).toFixed(2);
        } else {
          item.productivity = "0.00";
          item.unproductivity = "0.00";
        }
      }

      return res.json({
        code: 200,
        total: resultCount[0]?.myCount ?? 0,
        data: result,
        message: 'Productivity List.',
        error: null
      });
    } catch (err) {
      console.log(err);
      next(err);
    }
  }
}

module.exports = new ProductivityReportsController();
