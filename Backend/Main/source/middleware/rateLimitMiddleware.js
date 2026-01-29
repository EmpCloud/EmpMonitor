const rateLimit = require('express-rate-limit');

/**
 * Rate limiter for authentication endpoints
 * More restrictive to prevent brute force attacks
 */
const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 5, // Limit each IP to 5 requests per windowMs
  message: {
    code: 429,
    message: 'Too many authentication attempts, please try again after 15 minutes'
  },
  standardHeaders: true, // Return rate limit info in the `RateLimit-*` headers
  legacyHeaders: false, // Disable the `X-RateLimit-*` headers
});

/**
 * Rate limiter for dashboard and general API endpoints
 * More permissive for desktop application with multiple requests
 */
const dashboardLimiter = rateLimit({
  windowMs: 1 * 60 * 1000, // 1 minute window
  max: 200, // Limit each IP to 200 requests per minute
  message: {
    code: 429,
    message: 'Too many requests from this IP, please try again after 1 minute'
  },
  standardHeaders: true,
  legacyHeaders: false,
  // Skip for localhost/development
  skip: (req) => {
    return req.ip === '127.0.0.1' || req.ip === '::1' || req.ip === 'localhost';
  }
});

/**
 * Rate limiter for authenticated API endpoints
 * More permissive for desktop application usage
 */
const strictLimiter = rateLimit({
  windowMs: 1 * 60 * 1000, // 1 minute window for faster reset
  max: 100, // Limit each IP to 100 requests per minute
  message: {
    code: 429,
    message: 'Too many requests, please slow down'
  },
  standardHeaders: true,
  legacyHeaders: false,
  // Skip for localhost/development
  skip: (req) => {
    return req.ip === '127.0.0.1' || req.ip === '::1' || req.ip === 'localhost';
  }
});

/**
 * Very permissive rate limiter for read-only operations
 */
const readOnlyLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 200, // Limit each IP to 200 requests per windowMs
  message: {
    code: 429,
    message: 'Too many requests, please try again later'
  },
  standardHeaders: true,
  legacyHeaders: false,
});

module.exports = {
  authLimiter,
  dashboardLimiter,
  strictLimiter,
  readOnlyLimiter
};

