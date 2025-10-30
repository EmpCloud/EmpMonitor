const mySqlSingleton = require('../../database/MySqlConnection');

class MonitoringControlModel {
  // ============= RULE OPERATIONS =============
  
  /**
   * Get all monitoring rules
   */
  async getAllRules(skip = 0, limit = 10, search = '') {
    const pool = await mySqlSingleton.getPool();
    let query = `
      SELECT mr.*, 
             COUNT(DISTINCT re.employee_id) as employee_count
      FROM monitoring_rules mr
      LEFT JOIN rule_employees re ON mr.id = re.rule_id
    `;
    const params = [];

    if (search) {
      query += ` WHERE mr.rule_name LIKE ?`;
      params.push(`%${search}%`);
    }

    query += ` GROUP BY mr.id ORDER BY mr.is_default DESC, mr.created_at DESC LIMIT ? OFFSET ?`;
    params.push(limit, skip);

    const [rows] = await pool.query(query, params);
    return rows;
  }

  /**
   * Get all rules count
   */
  async getAllRulesCount(search = '') {
    const pool = await mySqlSingleton.getPool();
    let query = `SELECT COUNT(*) AS total FROM monitoring_rules`;
    const params = [];

    if (search) {
      query += ` WHERE rule_name LIKE ?`;
      params.push(`%${search}%`);
    }

    const [rows] = await pool.query(query, params);
    return rows[0].total;
  }

  /**
   * Get rule by ID
   */
  async getRuleById(id) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT * FROM monitoring_rules WHERE id = ?',
      [id]
    );
    return rows[0];
  }

  /**
   * Get rule by name
   */
  async getRuleByName(ruleName) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT * FROM monitoring_rules WHERE rule_name = ?',
      [ruleName]
    );
    return rows[0];
  }

  /**
   * Get default rule
   */
  async getDefaultRule() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT * FROM monitoring_rules WHERE is_default = 1 LIMIT 1'
    );
    return rows[0];
  }

  /**
   * Create monitoring rule
   */
  async createRule(ruleData) {
    const pool = await mySqlSingleton.getPool();
    const {
      ruleName,
      trackApplications = 1,
      trackWebsites = 1,
      trackKeystrokes = 1,
      trackScreenshots = 1,
      trackMouseClicks = 1,
      isDefault = 0,
      description = ''
    } = ruleData;

    const [result] = await pool.query(
      `INSERT INTO monitoring_rules 
       (rule_name, track_applications, track_websites, track_keystrokes, 
        track_screenshots, track_mouse_clicks, is_default, description) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [ruleName, trackApplications, trackWebsites, trackKeystrokes, 
       trackScreenshots, trackMouseClicks, isDefault, description]
    );
    return result.insertId;
  }

  /**
   * Update monitoring rule
   */
  async updateRule(id, ruleData) {
    const pool = await mySqlSingleton.getPool();
    const {
      ruleName,
      trackApplications,
      trackWebsites,
      trackKeystrokes,
      trackScreenshots,
      trackMouseClicks,
      description
    } = ruleData;

    await pool.query(
      `UPDATE monitoring_rules 
       SET rule_name = ?, track_applications = ?, track_websites = ?, 
           track_keystrokes = ?, track_screenshots = ?, track_mouse_clicks = ?, 
           description = ?, updated_at = NOW()
       WHERE id = ?`,
      [ruleName, trackApplications, trackWebsites, trackKeystrokes, 
       trackScreenshots, trackMouseClicks, description, id]
    );
  }

  /**
   * Delete monitoring rule
   */
  async deleteRule(id) {
    const pool = await mySqlSingleton.getPool();
    await pool.query('DELETE FROM monitoring_rules WHERE id = ?', [id]);
  }

  /**
   * Check if rule is in use
   */
  async isRuleInUse(ruleId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT COUNT(*) AS count FROM rule_employees WHERE rule_id = ?',
      [ruleId]
    );
    return rows[0].count > 0;
  }

  // ============= RULE EMPLOYEE ASSIGNMENT OPERATIONS =============

  /**
   * Get employees assigned to a rule
   */
  async getEmployeesByRule(ruleId, skip = 0, limit = 10) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT e.*, re.assigned_at, d.name as department_name
       FROM rule_employees re
       INNER JOIN employees e ON re.employee_id = e.id
       LEFT JOIN departments d ON e.department_id = d.id
       WHERE re.rule_id = ?
       LIMIT ? OFFSET ?`,
      [ruleId, limit, skip]
    );
    return rows;
  }

  /**
   * Get employees assigned to a rule count
   */
  async getEmployeesByRuleCount(ruleId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      'SELECT COUNT(*) AS total FROM rule_employees WHERE rule_id = ?',
      [ruleId]
    );
    return rows[0].total;
  }

  /**
   * Get rule assigned to an employee
   */
  async getRuleByEmployee(employeeId) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT mr.* 
       FROM monitoring_rules mr
       INNER JOIN rule_employees re ON mr.id = re.rule_id
       WHERE re.employee_id = ?`,
      [employeeId]
    );
    return rows[0];
  }

  /**
   * Assign employee to rule
   */
  async assignEmployeeToRule(employeeId, ruleId) {
    const pool = await mySqlSingleton.getPool();
    
    // First, remove employee from any other rule
    await pool.query(
      'DELETE FROM rule_employees WHERE employee_id = ?',
      [employeeId]
    );

    // Then assign to new rule
    await pool.query(
      'INSERT INTO rule_employees (rule_id, employee_id) VALUES (?, ?)',
      [ruleId, employeeId]
    );
  }

  /**
   * Assign multiple employees to rule
   */
  async assignMultipleEmployeesToRule(employeeIds, ruleId) {
    const pool = await mySqlSingleton.getPool();
    
    // Remove employees from any other rule
    if (employeeIds.length > 0) {
      const placeholders = employeeIds.map(() => '?').join(',');
      await pool.query(
        `DELETE FROM rule_employees WHERE employee_id IN (${placeholders})`,
        employeeIds
      );

      // Assign to new rule
      const values = employeeIds.map(empId => [ruleId, empId]);
      await pool.query(
        'INSERT INTO rule_employees (rule_id, employee_id) VALUES ?',
        [values]
      );
    }
  }

  /**
   * Remove employee from rule
   */
  async removeEmployeeFromRule(employeeId, ruleId) {
    const pool = await mySqlSingleton.getPool();
    await pool.query(
      'DELETE FROM rule_employees WHERE employee_id = ? AND rule_id = ?',
      [employeeId, ruleId]
    );
  }

  /**
   * Get unassigned employees
   */
  async getUnassignedEmployees(skip = 0, limit = 10) {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT e.*, d.name as department_name
       FROM employees e
       LEFT JOIN departments d ON e.department_id = d.id
       LEFT JOIN rule_employees re ON e.id = re.employee_id
       WHERE re.employee_id IS NULL AND e.role = 'employee'
       LIMIT ? OFFSET ?`,
      [limit, skip]
    );
    return rows;
  }

  /**
   * Get unassigned employees count
   */
  async getUnassignedEmployeesCount() {
    const pool = await mySqlSingleton.getPool();
    const [rows] = await pool.query(
      `SELECT COUNT(*) AS total
       FROM employees e
       LEFT JOIN rule_employees re ON e.id = re.employee_id
       WHERE re.employee_id IS NULL AND e.role = 'employee'`
    );
    return rows[0].total;
  }

  /**
   * Assign all unassigned employees to default rule
   */
  async assignUnassignedToDefault() {
    const pool = await mySqlSingleton.getPool();
    
    // Get default rule
    const [defaultRule] = await pool.query(
      'SELECT id FROM monitoring_rules WHERE is_default = 1 LIMIT 1'
    );

    if (defaultRule.length === 0) {
      throw new Error('Default rule not found');
    }

    const defaultRuleId = defaultRule[0].id;

    // Get unassigned employees
    const [unassigned] = await pool.query(
      `SELECT e.id
       FROM employees e
       LEFT JOIN rule_employees re ON e.id = re.employee_id
       WHERE re.employee_id IS NULL AND e.role = 'employee'`
    );

    if (unassigned.length > 0) {
      const values = unassigned.map(emp => [defaultRuleId, emp.id]);
      await pool.query(
        'INSERT INTO rule_employees (rule_id, employee_id) VALUES ?',
        [values]
      );
    }

    return unassigned.length;
  }
}

module.exports = new MonitoringControlModel();

