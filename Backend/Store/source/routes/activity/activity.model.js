const mySql = require('../../database/MySqlConnection').getInstance();

class ReportsModel {
    getEmployeeAttendance(id, date) {
        return mySql.query(`
            SELECT id, date, start_time, end_time FROM employee_attendance
            WHERE employee_id =? AND date =?
        `, [id, date]);
    }

    addEmployeeAttendance(id, date, start_time, end_time) {
        return mySql.query(`
            INSERT INTO employee_attendance (employee_id, date, start_time, end_time) VALUES (?, ?, ?, ?);
        `, [id, date, start_time, end_time]);
    }

    updateEmployeeAttendance(id, end_time) {
        return mySql.query(`
            UPDATE employee_attendance SET end_time = ? WHERE id = ?;
        `, [end_time, id]);
    }
}

module.exports = new ReportsModel();