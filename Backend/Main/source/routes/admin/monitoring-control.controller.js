const MonitoringControlModel = require('./monitoring-control.model');

class MonitoringControlController {
  // ============= RULE MANAGEMENT =============

  /**
   * Get all monitoring rules
   */
  async getAllRules(req, res) {
    try {
      const { skip = 0, limit = 10, search = '' } = req.query;

      const [rules, totalCount] = await Promise.all([
        MonitoringControlModel.getAllRules(+skip, +limit, search),
        MonitoringControlModel.getAllRulesCount(search)
      ]);

      return res.json({
        code: 200,
        data: { rules, totalCount },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error getting monitoring rules:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Get rule by ID
   */
  async getRuleById(req, res) {
    try {
      const { id } = req.params;

      const rule = await MonitoringControlModel.getRuleById(id);

      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'Rule not found'
        });
      }

      return res.json({
        code: 200,
        data: rule,
        message: 'Success'
      });
    } catch (error) {
      console.error('Error getting rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Create new monitoring rule
   */
  async createRule(req, res) {
    try {
      const {
        ruleName,
        description = ''
      } = req.body;

      // Convert tracking options to integers
      const trackApplications = parseInt(req.body.trackApplications) || 0;
      const trackWebsites = parseInt(req.body.trackWebsites) || 0;
      const trackKeystrokes = parseInt(req.body.trackKeystrokes) || 0;
      const trackScreenshots = parseInt(req.body.trackScreenshots) || 0;
      const trackMouseClicks = parseInt(req.body.trackMouseClicks) || 0;

      // Validate required fields
      if (!ruleName || !ruleName.trim()) {
        return res.status(400).json({
          code: 400,
          message: 'Rule name is required'
        });
      }

      // Check if rule name already exists
      const existingRule = await MonitoringControlModel.getRuleByName(ruleName.trim());
      if (existingRule) {
        return res.status(400).json({
          code: 400,
          message: 'Rule name already exists'
        });
      }

      // Validate tracking options (must be 0 or 1)
      const trackingOptions = {
        trackApplications,
        trackWebsites,
        trackKeystrokes,
        trackScreenshots,
        trackMouseClicks
      };

      for (const [key, value] of Object.entries(trackingOptions)) {
        if (value !== 0 && value !== 1) {
          return res.status(400).json({
            code: 400,
            message: `${key} must be either 0 (disabled) or 1 (enabled)`
          });
        }
      }

      const ruleId = await MonitoringControlModel.createRule({
        ruleName: ruleName.trim(),
        trackApplications,
        trackWebsites,
        trackKeystrokes,
        trackScreenshots,
        trackMouseClicks,
        isDefault: 0,
        description
      });

      return res.status(201).json({
        code: 201,
        data: {
          id: ruleId,
          ruleName: ruleName.trim(),
          trackApplications,
          trackWebsites,
          trackKeystrokes,
          trackScreenshots,
          trackMouseClicks,
          description
        },
        message: 'Monitoring rule created successfully'
      });
    } catch (error) {
      console.error('Error creating monitoring rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Update monitoring rule
   */
  async updateRule(req, res) {
    try {
      const { id } = req.params;
      const {
        ruleName,
        description
      } = req.body;

      // Convert tracking options to integers
      const trackApplications = req.body.trackApplications !== undefined ? parseInt(req.body.trackApplications) : undefined;
      const trackWebsites = req.body.trackWebsites !== undefined ? parseInt(req.body.trackWebsites) : undefined;
      const trackKeystrokes = req.body.trackKeystrokes !== undefined ? parseInt(req.body.trackKeystrokes) : undefined;
      const trackScreenshots = req.body.trackScreenshots !== undefined ? parseInt(req.body.trackScreenshots) : undefined;
      const trackMouseClicks = req.body.trackMouseClicks !== undefined ? parseInt(req.body.trackMouseClicks) : undefined;

      // Check if rule exists
      const rule = await MonitoringControlModel.getRuleById(id);
      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'Rule not found'
        });
      }

      // Prevent updating default rule name
      if (rule.is_default === 1 && ruleName && ruleName !== rule.rule_name) {
        return res.status(400).json({
          code: 400,
          message: 'Cannot rename default rule'
        });
      }

      // Validate rule name if provided
      if (ruleName && ruleName.trim() !== rule.rule_name) {
        const existingRule = await MonitoringControlModel.getRuleByName(ruleName.trim());
        if (existingRule) {
          return res.status(400).json({
            code: 400,
            message: 'Rule name already exists'
          });
        }
      }

      // Validate tracking options
      const trackingOptions = {
        trackApplications,
        trackWebsites,
        trackKeystrokes,
        trackScreenshots,
        trackMouseClicks
      };

      for (const [key, value] of Object.entries(trackingOptions)) {
        if (value !== undefined && value !== 0 && value !== 1) {
          return res.status(400).json({
            code: 400,
            message: `${key} must be either 0 (disabled) or 1 (enabled)`
          });
        }
      }

      await MonitoringControlModel.updateRule(id, {
        ruleName: ruleName ? ruleName.trim() : rule.rule_name,
        trackApplications: trackApplications !== undefined ? trackApplications : rule.track_applications,
        trackWebsites: trackWebsites !== undefined ? trackWebsites : rule.track_websites,
        trackKeystrokes: trackKeystrokes !== undefined ? trackKeystrokes : rule.track_keystrokes,
        trackScreenshots: trackScreenshots !== undefined ? trackScreenshots : rule.track_screenshots,
        trackMouseClicks: trackMouseClicks !== undefined ? trackMouseClicks : rule.track_mouse_clicks,
        description: description !== undefined ? description : rule.description
      });

      return res.json({
        code: 200,
        message: 'Rule updated successfully'
      });
    } catch (error) {
      console.error('Error updating monitoring rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Delete monitoring rule
   */
  async deleteRule(req, res) {
    try {
      const { id } = req.params;

      // Check if rule exists
      const rule = await MonitoringControlModel.getRuleById(id);
      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'Rule not found'
        });
      }

      // Prevent deleting default rule
      if (rule.is_default === 1) {
        return res.status(400).json({
          code: 400,
          message: 'Cannot delete default rule'
        });
      }

      // Check if rule has employees assigned
      const isInUse = await MonitoringControlModel.isRuleInUse(id);
      if (isInUse) {
        return res.status(400).json({
          code: 400,
          message: 'Cannot delete rule with assigned employees. Please reassign employees first.'
        });
      }

      await MonitoringControlModel.deleteRule(id);

      return res.json({
        code: 200,
        message: 'Rule deleted successfully'
      });
    } catch (error) {
      console.error('Error deleting monitoring rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  // ============= EMPLOYEE ASSIGNMENT =============

  /**
   * Get employees assigned to a rule
   */
  async getEmployeesByRule(req, res) {
    try {
      const { id } = req.params;
      const { skip = 0, limit = 10 } = req.query;

      // Check if rule exists
      const rule = await MonitoringControlModel.getRuleById(id);
      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'Rule not found'
        });
      }

      const [employees, totalCount] = await Promise.all([
        MonitoringControlModel.getEmployeesByRule(id, +skip, +limit),
        MonitoringControlModel.getEmployeesByRuleCount(id)
      ]);

      return res.json({
        code: 200,
        data: { employees, totalCount },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error getting employees by rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Assign employees to rule
   */
  async assignEmployeesToRule(req, res) {
    try {
      const { id } = req.params;
      const { employeeIds } = req.body;

      // Validate input
      if (!employeeIds || !Array.isArray(employeeIds) || employeeIds.length === 0) {
        return res.status(400).json({
          code: 400,
          message: 'employeeIds must be a non-empty array'
        });
      }

      // Check if rule exists
      const rule = await MonitoringControlModel.getRuleById(id);
      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'Rule not found'
        });
      }

      await MonitoringControlModel.assignMultipleEmployeesToRule(employeeIds, id);

      return res.json({
        code: 200,
        message: `${employeeIds.length} employee(s) assigned to rule successfully`
      });
    } catch (error) {
      console.error('Error assigning employees to rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Assign single employee to rule
   */
  async assignEmployeeToRule(req, res) {
    try {
      const { employeeId, ruleId } = req.body;

      // Validate input
      if (!employeeId || !ruleId) {
        return res.status(400).json({
          code: 400,
          message: 'employeeId and ruleId are required'
        });
      }

      // Check if rule exists
      const rule = await MonitoringControlModel.getRuleById(ruleId);
      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'Rule not found'
        });
      }

      await MonitoringControlModel.assignEmployeeToRule(employeeId, ruleId);

      return res.json({
        code: 200,
        message: 'Employee assigned to rule successfully'
      });
    } catch (error) {
      console.error('Error assigning employee to rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Get unassigned employees
   */
  async getUnassignedEmployees(req, res) {
    try {
      const { skip = 0, limit = 10 } = req.query;

      const [employees, totalCount] = await Promise.all([
        MonitoringControlModel.getUnassignedEmployees(+skip, +limit),
        MonitoringControlModel.getUnassignedEmployeesCount()
      ]);

      return res.json({
        code: 200,
        data: { employees, totalCount },
        message: 'Success'
      });
    } catch (error) {
      console.error('Error getting unassigned employees:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Assign all unassigned employees to default rule
   */
  async assignUnassignedToDefault(req, res) {
    try {
      const count = await MonitoringControlModel.assignUnassignedToDefault();

      return res.json({
        code: 200,
        data: { assignedCount: count },
        message: `${count} employee(s) assigned to default rule`
      });
    } catch (error) {
      console.error('Error assigning unassigned to default:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }

  /**
   * Get employee's current rule
   */
  async getEmployeeRule(req, res) {
    try {
      const { employeeId } = req.params;

      const rule = await MonitoringControlModel.getRuleByEmployee(employeeId);

      if (!rule) {
        return res.status(404).json({
          code: 404,
          message: 'No rule assigned to this employee'
        });
      }

      return res.json({
        code: 200,
        data: rule,
        message: 'Success'
      });
    } catch (error) {
      console.error('Error getting employee rule:', error);
      return res.status(500).json({
        message: 'Internal server error',
        error: error.message
      });
    }
  }
}

module.exports = new MonitoringControlController();

