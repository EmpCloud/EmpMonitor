const ReportModel = require('./reports.model');
const moment = require("moment-timezone");
const _ = require("underscore");
class ReportsController {
    async ["addData"](_0x4c6104, _0x2a9f04, _0x4ee776) {
        let _0x2541c5 = _0x4c6104.user;
        console.log(_0x2541c5);
        try {
            // Get employee's monitoring rule to check what to track
            let monitoringRule = await ReportModel.getEmployeeMonitoringRule(_0x2541c5.id);
            
            // If no rule found, allow all tracking (default behavior)
            if (!monitoringRule) {
                console.log('No monitoring rule found for employee:', _0x2541c5.id, '- allowing all tracking');
                monitoringRule = {
                    track_applications: 1,
                    track_websites: 1,
                    track_keystrokes: 1,
                    track_screenshots: 1,
                    track_mouse_clicks: 1
                };
            }

            let {
                ..._0x372751
            } = _0x4c6104.body;
            let _0x20247b = {
                'employee_id': _0x2541c5.id,
                ..._0x372751
            };
            let _0x2947cf = [];
            for (const _0x50d7fa of _0x20247b.data) {
                let _0x5e89d3 = moment.utc(_0x50d7fa.dataId);
                for (let _0x4dbab8 of _0x50d7fa.appUsage) {
                    let _0x15aaa6 = _0x4dbab8.start;
                    let _0x392d18 = _0x4dbab8.end;
                    let _0x1a67a2 = moment.utc(_0x5e89d3).add(_0x15aaa6, "second");
                    let _0xc7c4ea = moment.utc(_0x5e89d3).add(_0x392d18, "second");
                    let _0x4567bd = _0x50d7fa.activityPerSecond.keystrokes.slice(_0x15aaa6, _0x392d18).filter(_0x41bb5e => +_0x41bb5e !== 0x0).length;
                    let _0x2be3bc = _0x50d7fa.activityPerSecond.mouseMovements.slice(_0x15aaa6, _0x392d18).filter(_0x49e5a8 => +_0x49e5a8 !== 0x0).length;
                    let _0x4188ab = _0x50d7fa.activityPerSecond.buttonClicks.slice(_0x15aaa6, _0x392d18).filter(_0x12f637 => +_0x12f637 !== 0x0).length;
                    if (_0x15aaa6 === _0x392d18) {
                        _0x392d18 = _0x392d18 + 0x1;
                    }
                    let {
                        active_seconds: _0x226487,
                        total_seconds: _0x2e194c
                    } = calculateActivity(_0x15aaa6, _0x392d18, _0x50d7fa.activityPerSecond, _0x50d7fa.appUsage);
                    let _0x20556b = null;
                    let _0x58e6d9 = null;
                    let _0x191bec = null;
                    
                    // Only track applications if enabled
                    if (monitoringRule.track_applications === 1 && _0x4dbab8.app && _0x4dbab8.app.trim() !== '') {
                        const _0x7b235b = await ReportModel.findOrCreateOrgAppWeb(_0x2541c5.organization_id || 0x1, 0x1, _0x4dbab8.app);
                        _0x20556b = _0x7b235b._id;
                        _0x191bec = _0x7b235b;
                    }
                    
                    // Only track websites if enabled
                    if (monitoringRule.track_websites === 1 && _0x4dbab8.url && _0x4dbab8.url.trim() !== '') {
                        const _0x70720a = ReportModel.extractDomain(_0x4dbab8.url);
                        if (_0x70720a) {
                            const _0x29e74a = await ReportModel.findOrCreateOrgAppWeb(_0x2541c5.organization_id || 0x1, 0x2, _0x70720a);
                            _0x58e6d9 = _0x29e74a._id;
                            _0x191bec = _0x29e74a;
                        }
                    }
                    
                    // Skip this record if both applications and websites tracking are disabled
                    if (monitoringRule.track_applications === 0 && monitoringRule.track_websites === 0) {
                        continue;
                    }
                    let _0xd63741;
                    const _0x3a53c3 = await ReportModel.findMergeableRecord(_0x2541c5.id, _0x4dbab8.app, _0x4dbab8.title, _0x4dbab8.url, _0x1a67a2.toISOString());
                    if (_0x3a53c3) {
                        const _0x55ecc1 = {
                            'end_time': _0xc7c4ea.toISOString(),
                            'active_seconds': _0x3a53c3.active_seconds + _0x226487,
                            'total_seconds': _0x3a53c3.total_seconds + _0x2e194c,
                            'idle_seconds': _0x3a53c3.idle_seconds + (_0x2e194c - _0x226487)
                        };
                        
                        // Only save keystrokes if enabled
                        if (monitoringRule.track_keystrokes === 1) {
                            _0x55ecc1.keystrokes = _0x3a53c3.keystrokes + (_0x4dbab8.keystrokes ?? '');
                            _0x55ecc1.keystrokesCount = _0x3a53c3.keystrokesCount + _0x4567bd;
                        }
                        
                        // Only save mouse clicks if enabled
                        if (monitoringRule.track_mouse_clicks === 1) {
                            _0x55ecc1.mouseMovementsCount = _0x3a53c3.mouseMovementsCount + _0x2be3bc;
                            _0x55ecc1.buttonClicks = _0x3a53c3.buttonClicks + _0x4188ab;
                        }
                        if (_0x20556b && !_0x3a53c3.application_id) {
                            _0x55ecc1.application_id = _0x20556b;
                        }
                        if (_0x58e6d9 && !_0x3a53c3.domain_id) {
                            _0x55ecc1.domain_id = _0x58e6d9;
                        }
                        if (_0x191bec && _0x191bec.category == 0x0) {
                            _0x55ecc1.neutral_seconds = _0x3a53c3.neutral_seconds + _0x226487;
                        } else {
                            if (_0x191bec && _0x191bec.category == 0x1) {
                                _0x55ecc1.productive_seconds = _0x3a53c3.productive_seconds + _0x226487;
                            } else if (_0x191bec && _0x191bec.category == 0x2) {
                                _0x55ecc1.unproductive_seconds = _0x3a53c3.unproductive_seconds + _0x226487;
                            }
                        }
                        _0xd63741 = await ReportModel.updateRecord(_0x3a53c3._id, _0x55ecc1);
                    } else {
                        const newUsageData = {
                            'employee_id': _0x2541c5.id,
                            'organization_id': _0x2541c5.organization_id || 0x1,
                            'start_time': _0x1a67a2.toISOString(),
                            'end_time': _0xc7c4ea.toISOString(),
                            'yyyymmdd': _0x5e89d3.format("YYYYMMDD"),
                            'active_seconds': _0x226487,
                            'total_seconds': _0x2e194c,
                            'idle_seconds': _0x2e194c - _0x226487,
                            'neutral_seconds': _0x191bec && _0x191bec.category == 0x0 ? _0x226487 : 0x0,
                            'productive_seconds': _0x191bec && _0x191bec.category == 0x1 ? _0x226487 : 0x0,
                            'unproductive_seconds': _0x191bec && _0x191bec.category == 0x2 ? _0x226487 : 0x0
                        };
                        
                        // Only save application data if enabled
                        if (monitoringRule.track_applications === 1) {
                            newUsageData.application_id = _0x20556b;
                            newUsageData.application_name = _0x4dbab8.app;
                            newUsageData.title = _0x4dbab8.title;
                        }
                        
                        // Only save website data if enabled
                        if (monitoringRule.track_websites === 1) {
                            newUsageData.domain_id = _0x58e6d9;
                            newUsageData.url = _0x4dbab8.url;
                        }
                        
                        // Only save keystrokes if enabled
                        if (monitoringRule.track_keystrokes === 1) {
                            newUsageData.keystrokes = _0x4dbab8.keystrokes ?? '';
                            newUsageData.keystrokesCount = _0x4567bd;
                        } else {
                            newUsageData.keystrokes = '';
                            newUsageData.keystrokesCount = 0;
                        }
                        
                        // Only save mouse clicks if enabled
                        if (monitoringRule.track_mouse_clicks === 1) {
                            newUsageData.mouseMovementsCount = _0x2be3bc;
                            newUsageData.buttonClicks = _0x4188ab;
                        } else {
                            newUsageData.mouseMovementsCount = 0;
                            newUsageData.buttonClicks = 0;
                        }
                        
                        _0xd63741 = await ReportModel.addAppUsage(newUsageData);
                    }
                    _0x2947cf.push(_0xd63741);
                }
            }
            return _0x2a9f04.status(0xc8).json({
                'code': 0xc8,
                'message': "Data added successfully",
                'data': _0x2947cf
            });
        } catch (_0x1df960) {
            console.log(_0x1df960);
            _0x4ee776(_0x1df960);
        }
    }
}

module.exports = new ReportsController();

const calculateActivity = (_0x1cd9fb, _0x5320f5, _0x2eae06, _0x4bcf47) => {
    let {
        buttonClicks: _0x2ef895,
        fakeActivities: _0x3ae7fa,
        keystrokes: _0x239a70,
        mouseMovements: _0x275df0
    } = _0x2eae06;
    const _0x38b19c = _0x2ef895.length !== _0x4bcf47[_0x4bcf47.length - 0x1].end && _0x2ef895.length == _0x4bcf47[_0x4bcf47.length - 0x1].end + 0x1 && _0x4bcf47[_0x4bcf47.length - 0x1].end == _0x5320f5 && !(_0x2ef895.length - 0x1 == _0x1cd9fb);
    if (_0x38b19c) {
        _0x2ef895 = _0x2ef895.slice(_0x1cd9fb, _0x4bcf47[_0x4bcf47.length - 0x1].end + 0x1);
        _0x3ae7fa = _0x3ae7fa.slice(_0x1cd9fb, _0x4bcf47[_0x4bcf47.length - 0x1].end + 0x1);
        _0x239a70 = _0x239a70.slice(_0x1cd9fb, _0x4bcf47[_0x4bcf47.length - 0x1].end + 0x1);
        _0x275df0 = _0x275df0.slice(_0x1cd9fb, _0x4bcf47[_0x4bcf47.length - 0x1].end + 0x1);
    } else {
        _0x2ef895 = _0x2ef895.slice(_0x1cd9fb, _0x5320f5);
        _0x3ae7fa = _0x3ae7fa.slice(_0x1cd9fb, _0x5320f5);
        _0x239a70 = _0x239a70.slice(_0x1cd9fb, _0x5320f5);
        _0x275df0 = _0x275df0.slice(_0x1cd9fb, _0x5320f5);
    }
    const _0x1c66ac = _.chunk(_0x2ef895, 0x3c);
    const _0x221d90 = _.chunk(_0x3ae7fa, 0x3c);
    const _0x27d82b = _.chunk(_0x239a70, 0x3c);
    const _0x21d1c3 = _.chunk(_0x275df0, 0x3c);
    const _0x1d0df8 = _0x1c66ac.reduce((_0x37500b, _0x51935c, _0x19a167, _0x1390aa) => {
        return _0x1c66ac[_0x19a167].some(_0x4faa02 => _0x4faa02 > 0x0) || _0x221d90[_0x19a167].some(_0x561da7 => _0x561da7 > 0x0) || _0x27d82b[_0x19a167].some(_0x6bc399 => _0x6bc399 > 0x0) || _0x21d1c3[_0x19a167].some(_0x43128f => _0x43128f > 0x0) ? _0x37500b + _0x51935c.length : _0x37500b + 0x0;
    }, 0x0);
    return {
        'active_seconds': _0x1d0df8,
        'total_seconds': _0x5320f5 - _0x1cd9fb,
        'mouseclicks_count': _0x2ef895.filter(_0x173d77 => _0x173d77 !== 0x0).length,
        'keystrokes_count': _0x239a70.filter(_0x18fa2c => _0x18fa2c !== 0x0).length,
        'mousemovement_count': _0x275df0.filter(_0x2bcb35 => _0x2bcb35 !== 0x0).length,
        'condition': _0x38b19c,
        'chunkData': {
            'buttonClickChunks': _0x1c66ac,
            'fakeActivityChunks': _0x221d90,
            'keystrokeChunks': _0x27d82b,
            'mouseMovementChunks': _0x21d1c3
        }
    };
};