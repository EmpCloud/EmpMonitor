const ReportModel = require('./activity.model');
const moment = require('moment-timezone');
const { default : axios } = require('axios');

class ActivityController {
    async addActivity (req, res, next) {
        let user = req.user;
        try {
            let { sign, data } = req.body;
            let attendanceId;
            for (const e of data) {
                let date = moment(e.dataId).format('YYYY-MM-DD');
                let start_time = moment.utc(e.dataId).format('YYYY-MM-DD HH:mm:ss');
                let end_time = moment.utc(e.dataId).add(e.mode.end, 'second').format('YYYY-MM-DD HH:mm:ss');
                let [previousAttendance] = await ReportModel.getEmployeeAttendance(user.id, date);
                if(previousAttendance) {
                    let res = await ReportModel.updateEmployeeAttendance(previousAttendance.id, end_time);
                    attendanceId = previousAttendance.id;
                }
                else {
                    let res = await ReportModel.addEmployeeAttendance(user.id, date, start_time, end_time);
                    attendanceId = res.insertId;
                }
            }
            data.attendanceId = attendanceId;
            await axios.post(process.env.REPORT_SERVER_URL, {
                data: data,
                attendanceId: attendanceId
            }, {
                headers: {
                    Authorization: `Bearer ${user.token}`
                }
            });

            return res.status(200).json({
                code: 200,
                message: 'Data inserted successfully',
                data: data.length
            })
        } catch (error) {
            next(error);
        }
    }
}

module.exports = new ActivityController();