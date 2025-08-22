const responseHandler = require('./responseHandler.js');

const error = (err, req, res, next) => {
    switch (err.name) {
        case 'ValidationError':
            return responseHandler(res, 400, null, "Validation Error", err.message);
            break;
    
        case 'InvalidToken':
            return responseHandler(res, 401, null, "Invalid Token", 'Unauthorized access');
            break;

        case 'MongoServerError':
            return databaseError(err, req, res, next);
            break;

        default:
            break;
    }
    return res.status(500).json({ message: 'Something went wrong', error: err.message });
}


const databaseError = (err, req, res, next) => {
    if(err.message.includes('duplicate key error collection') && err.message.includes('email')) return responseHandler(res, 400, null, "Email already exist", err.message);
    return res.status(500).json({ message: 'Something went wrong', error: err.message });
}


module.exports = error;