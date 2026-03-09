const crypto = require('crypto');
const jwt = require('jsonwebtoken');
require('dotenv').config();

const algorithm = 'aes-256-cbc';
const key = crypto.createHash('sha256').update(process.env.JWT_SECRET).digest('base64').substr(0, 32);

const MySql = require('../../database/MySqlConnection').getInstance();

class AuthModel {

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
            if (!encryptedPassword.includes(':')) throw new Error('Invalid encrypted password format.');
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
        try {
            return jwt.sign(payload, process.env.JWT_SECRET, { expiresIn: '1h' });
        } catch (error) {
            console.error('Error in generateToken:', error);
            throw error;
        }
    }

    async getEmployeeByEmail(email) {
        try {
            const query = 'SELECT * FROM employees WHERE email = ? AND role = ?';
            const results = await MySql.query(query, [email, 'employee']);
            return results[0];
        } catch (error) {
            console.error('Error in getEmployeeByEmail: ', error);
            throw error;
        }
    }
}

module.exports = new AuthModel();