const mysql = require('mysql2/promise');
require('dotenv').config();

let pool;

async function createPool() {
  try {
    pool = mysql.createPool({
      host: process.env.DB_HOST,
      port: process.env.DB_PORT,
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      database: process.env.DB_DATABASE,
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0,
    });

    // Test the connection
    await pool.query('SELECT 1');
    console.log('Database connection successful');

    // Handle connection errors
    pool.on('error', (err) => {
      console.error('Database pool error:', err);
      if (err.code === 'PROTOCOL_CONNECTION_LOST') {
        console.error('Database connection was closed.');
      }
      if (err.code === 'ER_CON_COUNT_ERROR') {
        console.error('Database has too many connections.');
      }
      if (err.code === 'ECONNREFUSED') {
        console.error('Database connection was refused.');
      }
    });

    return pool;
  } catch (error) {
    console.error('Error creating database connection pool:', error);
    process.exit(1); // Exit if the connection fails.
  }
}

async function getPool() {
  if (!pool) {
    pool = await createPool();
  }
  return pool;
}

module.exports = { getPool };