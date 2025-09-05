
require('express-async-errors');

const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const compression = require('compression');
require('dotenv').config();
const path = require('path');

const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('./source/utils/swagger/swagger.json');


const connectToMongoDB = require('./source/database/MongoConnection.js');

const initRoutes = require('./source/routes/routes.js');

const errorHandler = require('./source/utils/helper/errorHelper.js');

const initServer = async () => {
    try {
        connectToMongoDB.connect();

        const app = express();

        const PORT = process.env.PORT || 3080;
        const publicFolderPath = path.resolve('public');

        app.use(express.json({ limit: '50mb' }));
        app.use(express.urlencoded({ limit: '50mb', extended: true }));
        app.use(cors());
        app.use(helmet());
        app.use(morgan('dev'));
        app.use(compression());


        app.use(express.static(publicFolderPath));

        app.use((req, res, next) => {
            res.set({
                'Connection': 'keep-alive',
                'Keep-Alive': 'timeout=300',
            });
            next();
        });

        app.get('/', (req, res) => {
            return res.status(200).json({ message: 'Success' });
        });

        app.use('/api-docs', swaggerUi.serve, swaggerUi.setup(swaggerDocument));

        app.use(new initRoutes().getRouters());

        app.use(errorHandler);

        app.listen(PORT, () => {
            console.log(`Server is running on http://localhost:${PORT}`);
        });
    } catch (err) {
        console.error('Failed to initialize the server:', err.message);
        process.exit(1);
    }
};

initServer();
