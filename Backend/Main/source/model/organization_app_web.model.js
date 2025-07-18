const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const WebAppActivitySchema = new Schema({
    organization_id: { type: Number, required: true },
    type: { type: Number, required: true }, // 1 for app, 2 for web
    name: { type: String, required: true }
}, { timestamps: true });

WebAppActivitySchema.index({ organization_id: 1, type: 1, name: 1 });
const WebAppActivityModel = mongoose.model('organization_app_web', WebAppActivitySchema);

module.exports = WebAppActivityModel;