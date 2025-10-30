const WebAppModel = require("../../model/web_app_activity.model");
const OrganizationAppWebModel = require('../../model/organization_app_web.model');
const mySqlSingleton = require('../../database/MySqlConnection');

class ReportsModel {
    ["addAppUsage"](_0x54e501) {
        return new WebAppModel(_0x54e501).save();
    }
    ["getPreviousUsage"](_0x1c4519) {
        return WebAppModel.findOne(_0x1c4519);
    }
    ["findMergeableRecord"](_0x2ffda2, _0x4b2d55, _0x3929a3, _0x3e986d, _0x10d3bc) {
        const _0x164f67 = {
            'employee_id': _0x2ffda2,
            'application_name': _0x4b2d55,
            'title': _0x3929a3,
            'end_time': _0x10d3bc
        };
        if (_0x3e986d && _0x3e986d.trim() !== '') {
            _0x164f67.url = _0x3e986d;
        }
        return WebAppModel.findOne(_0x164f67);
    }
    ["updateRecord"](_0x5efa75, _0x54406a) {
        return WebAppModel.findByIdAndUpdate(_0x5efa75, _0x54406a, {
            'new': true
        });
    }
    async ["findOrCreateOrgAppWeb"](_0x303acb, _0x27ff3e, _0x4ae4d6) {
        let _0x1989d8 = await OrganizationAppWebModel.findOne({
            'organization_id': _0x303acb,
            'type': _0x27ff3e,
            'name': _0x4ae4d6
        });
        if (!_0x1989d8) {
            _0x1989d8 = await new OrganizationAppWebModel({
                'organization_id': _0x303acb,
                'type': _0x27ff3e,
                'name': _0x4ae4d6
            }).save();
        }
        return _0x1989d8;
    }
    ["extractDomain"](_0x5e1a76) {
        if (!_0x5e1a76 || _0x5e1a76.trim() === '') {
            return null;
        }
        try {
            const _0x4a4545 = new URL(_0x5e1a76);
            return _0x4a4545.hostname;
        } catch (_0xd3b8f9) {
            const _0x2068fd = _0x5e1a76.match(/^(?:https?:\/\/)?([^\/]+)/);
            return _0x2068fd ? _0x2068fd[0x1] : null;
        }
    }

    /**
     * Get employee's monitoring rule
     * Returns the rule with tracking settings for the employee
     */
    async getEmployeeMonitoringRule(employeeId) {
        try {
            const pool = mySqlSingleton.getInstance();
            // In the mysql driver (not mysql2), the promisified query resolves to "results" only (no [rows, fields])
            const rows = await pool.query(
                `SELECT mr.* 
                 FROM monitoring_rules mr
                 INNER JOIN rule_employees re ON mr.id = re.rule_id
                 WHERE re.employee_id = ?
                 LIMIT 1`,
                [employeeId]
            );

            if (!rows || (Array.isArray(rows) && rows.length === 0)) {
                const defaultRows = await pool.query(
                    'SELECT * FROM monitoring_rules WHERE is_default = 1 LIMIT 1'
                );
                return Array.isArray(defaultRows) && defaultRows.length > 0 ? defaultRows[0] : null;
            }

            // If mysql driver returns an object (non-array), normalize it; otherwise return first row
            return Array.isArray(rows) ? rows[0] : rows;
        } catch (error) {
            console.error('Error getting employee monitoring rule:', error);
            return null;
        }
    }
}
module.exports = new ReportsModel();