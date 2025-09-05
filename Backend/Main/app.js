require('dotenv').config();
const express = require('express');
const adminRoutes = require('./source/routes/admin/admin.routes');
const employeeRoutes = require('./source/routes/employee/employee.routes');
const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('./source/utils/swagger/swagger.json');
const mongoDB = require('./source/database/MongoConnection');

const helmet = require('helmet');
const compression = require('compression');
const morgan = require('morgan');

const cors = require('cors');


const initServer = async () => {
  try {
    await mongoDB.connect();
    const app = express();
    const port = process.env.PORT || 3000;
  
    app.use(express.json({ limit: '50mb' }));
    app.use(express.urlencoded({ limit: '50mb', extended: true }));
    app.use(cors());
    app.use(helmet());
    app.use(morgan('dev'));
    app.use(compression());
  
    app.use('/admin', new adminRoutes().getRouters());
    app.use('/employee', new employeeRoutes().getRouters());
    app.use('/api-docs', swaggerUi.serve, swaggerUi.setup(swaggerDocument));
  
    app.listen(port, () => {
      console.log(`Server listening on port ${port}`);
    });
  } catch (error) {
    console.error('Failed to initialize the server:', error.message);
    process.exit(1);
  }
}

initServer();