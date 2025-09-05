const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const WebAppActivitySchema = new Schema({
    employee_id: { type: Number, required: true },
    organization_id: { type: Number, required: true },
    application_id: { type: mongoose.Schema.Types.ObjectId, ref: 'organization_app_web' },
    domain_id: { type: mongoose.Schema.Types.ObjectId, ref: 'organization_app_web' },
    start_time: { type: String, required: true },
    end_time: { type: String, },
    yyyymmdd: { type: Number, required: true },
    application_name: { type: String, },
    title: { type: String, },
    url: { type: String },
    keystrokes: { type: String, },
    keystrokesCount: { type: Number, },
    mouseMovementsCount: { type: Number, },
    buttonClicks:  { type: Number, },
    active_seconds: { type: Number, default: 0 },
    total_seconds: { type: Number, default: 0 },
    idle_seconds: { type: Number, default: 0 },
    neutral_seconds: { type: Number, default: 0 },
    productive_seconds: { type: Number, default: 0 },
    unproductive_seconds: { type: Number, default: 0 },
}, { timestamps: true });
WebAppActivitySchema.index({ organization_id: 1, employee_id: 1 });

const WebAppActivityModel = mongoose.model('web-app-activity', WebAppActivitySchema);

module.exports = WebAppActivityModel;