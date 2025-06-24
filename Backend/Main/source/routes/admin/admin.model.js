const crypto = require('crypto');
const jwt = require('jsonwebtoken');
require('dotenv').config();
const mySqlSingleton = require('../../database/MySqlConnection');

const algorithm = 'aes-256-cbc';
const key = crypto.createHash('sha256').update(process.env.JWT_SECRET).digest('base64').substr(0, 32);
class AdminModel{
encryptPassword(password) {
  try {
    const iv = crypto.randomBytes(16);
    const cipher = crypto.createCipheriv(algorithm, key, iv);
    let encrypted = cipher.update(password, 'utf8', 'hex');
    encrypted += cipher.final('hex');
    return `${iv.toString('hex')}:${encrypted}`;
  } catch (error) {
    console.error('Error in encryptPassword: ', error);
    throw error;
  }
}

decryptPassword(userPassword, encryptedPassword) {
  try {
    const [ivHex, encrypted] = encryptedPassword.split(':');
    const iv = Buffer.from(ivHex, 'hex');
    const decipher = crypto.createDecipheriv(algorithm, key, iv);
    let decrypted = decipher.update(encrypted, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    if (userPassword) return userPassword === decrypted;
    return decrypted;
  } catch (error) {
    console.error('Error in decryptPassword: ', error);
    throw error;
  }
}

generateToken(payload) {
  return jwt.sign(payload, process.env.JWT_SECRET, { expiresIn: '1d' });
}

async getLoginUserData(req) {
  try {
      const authHeader = req.headers['authorization'];
      const token = authHeader && authHeader.split(' ')[1];
      if (token == null) {
        console.error('Token not provided: ', error);
        throw error;
      }

      const user = jwt.verify(token, process.env.JWT_SECRET);

      return user;
  } catch (error) {
    console.error('Error in decryptPassword: ', error);
    throw error;
  }
  
}
  async getAdminByEmail(email) {
    try {
      const pool = await mySqlSingleton.getPool();
      if (!email) {
        throw new Error('Email is required to get admin data');
      }
      email = email.toLowerCase().trim();
      if (!email) {
        console.error('Email is empty or invalid');
        return null;
      }
      if (!pool) {
        console.error('Database connection pool is not initialized');
        throw new Error('Database connection error');
      }
      const query = 'SELECT * FROM admins WHERE email = ? LIMIT 1';
      const [rows] = await pool.execute(query, [email]);
      if (rows.length === 0) return null;
      return rows[0];
    } catch (error) {
      console.error('Error in getAdminByEmail: ', error);
      throw error;
    }
  }
    async getAdminLicenseCount(adminId) {
        try {
            const pool = await mySqlSingleton.getPool();
            const query = 'SELECT license FROM admins WHERE id = ? LIMIT 1';
            const [rows] = await pool.execute(query, [adminId]);
            if (rows.length === 0) return null;
            return rows[0].license;
        } catch (error) {
            console.error(`Error in getAdminLicenseCount for adminId ${adminId}: `, error);
            throw error;
        }
    }

    async updateAdminLicense(adminId, newLicense) {
        try {
            const pool = await mySqlSingleton.getPool();
            const query = 'UPDATE admins SET license = ? WHERE id = ?';
            const [result] = await pool.execute(query, [newLicense, adminId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error(`Error updating admin license for adminId ${adminId}: `, error);
            throw error;
        }
    }
}
module.exports = new AdminModel();