<div align="right">
  <img src="https://img.shields.io/badge/OpenSource-000?style=for-the-badge&logo=ghost&logoColor=black&color=ffd700" alt="OpenSource-Badge">
</div>

![EmpMonitor](/assets/EMPMonitor%20logo.png)
<p align="center"><i>Your Workforce Productivity Compass</i></p>

> **_EmpMonitor: The Ultimate Solution for Workforce Monitoring and Productivity Optimization_**



## ➤ Installation Guide

This guide will walk you through the installation process step by step, designed for users with minimal technical background. By following these instructions carefully, you'll be able to set up the software independently.


###  📍 Step 1: Requirement Check  
Before installing the software, ensure that your system meets the following requirements.

#### 1. Node.js
- Node.js is a runtime environment for executing JavaScript code outside a browser. Run the following command to check if Node.js is installed on your system.
- Open your computer's terminal or command prompt and type:
   ```sh
      node -v
   ```
   - ✔️ Expected output: `NodeJS 14.x` or later.
> [!TIP]
>
> If NodeJS is not installed, visit [NodeJS Official Website and download](https://nodejs.org/en) the LTS (Long Term Support) version.


####  2. NPM (Node Package Manager)
- NPM helps install required dependencies. To verify its installation, use the command below:
   ```sh
      npm -v
   ```
   - ✔️ Expected output: `NPM 7.x.x` or later.
> [!NOTE]
>
> Installing Node.js also installs NPM.


#### 3. Redis
- Redis is a database that speeds up application performance. Check if it is installed by running:
   ```sh
      redis-server --version
   ```
   - ✔️ Expected output: Redis version information.
   - If Redis is not installed, download and install it from the [official Redis website](https://redis.io/downloads/)

> [!WARNING]
>
> Redis is crucial for caching and real-time features. Ensure it's properly installed.

#### 4. MongoDB
- MongoDB is a NoSQL database required for storing data. To check if it is installed, run:
   ```sh
      mongod --version
   ```
   - ✔️ Expected output: MongoDB version information.
   - If MongoDB is not installed, download and install it from the [official MongoDB website](https://www.mongodb.com/try/download/community)

#### 5. MariaDB
- MariaDB is a relational database management system. Verify its installation with the following command:
   ```sh
      mysql --version
   ```
   - ✔️ Expected output: MariaDB version information.
   - If MariaDB is not installed, download and install it from the [official MariaDB website](https://mariadb.org/download/?t=mariadb&p=mariadb&r=11.7.2&os=windows&cpu=x86_64&pkg=msi&mirror=bharat)

> [!WARNING]
>
> Ensure all these software components are installed before proceeding!


---
<!-- Step 2 begins from here -->
### 📍 Step 2: Set Up NodeJS Project

#### 1. Create Project Directory
- Creating a dedicated directory helps organize your project and keeps all related files in one place. This step prepares the foundation for your software installation.
   ```bash
      # This command creates a new directory and moves into it
      mkdir employee-monitor
      cd employee-monitor
   ```

#### 2. Initialize Node Project
- Initializing a Node project sets up the basic configuration files needed for your application. The -y flag automatically accepts default settings.
   ```bash
      # Creates a new Node.js project with default settings
      npm init -y
   ```

---

<!-- step 3 begins here -->
### 📍 Step 3: Install Dependencies
#### 1. Backend Dependencies
Dependencies are external packages required for the software to function correctly. To install all necessary dependencies, use the following command:
   ```sh
      npm install
   ```
> [!TIP]
>
> This step might take a few minutes. Ensure you have a stable internet connection.




---
<!-- Step 4 begins here -->
### 📍 Step 4: Generate Application Key
#### 1. Generate Key
- Laravel requires an application key for security purposes. To generate this key, run the following command:
   ```sh
      php artisan key:generate
   ```
   - ✔️ Expected output: Application key set successfully.
> [!WARNING]
>
> If you skip this step, your application may not function correctly.
 




---
 <!-- Step 5 starts here -->
 ### 📍 Step 5: Configure the Database
#### 1. MariaDB Setup
- To create a new database in MariaDB, follow these steps:
   - Ensure MariaDB is running.
   - Open a terminal and log in to MariaDB:
  ```sh
      mysql -u root -p
  ```
  - Enter your MariaDB root password.
  - Create a new database by running:
  ```sh
      CREATE DATABASE empmonitor;
      EXIT;
  ```

#### 2. MongoDB Setup
- MongoDB does not require manual table creation. To set up a database, use the following commands:
   ```sh
      mongo
      use empmonitor;
      exit;
   ```
> [!TIP]
>
> Laravel will handle collections automatically.


#### 3. Redis Setup
- To check if Redis is running, execute the following command:
   ```sh
      redis-cli ping
   ```
   - ✔️ Expected output: `PONG`
- If Redis is not running, start it with:
   ```sh
      redis-server
   ```
> [!TIP]
>
> Keep Redis running in the background for better performance.

### 📍 Step 6: Compile Backend Assets
- To bundle application assets and start the development server, use the command below:

   For using default admin and user credentials, add the following `JWT_SECRET` in the `.env` file of all your services:

   ```env
   JWT_SECRET=tfwsIg87P49M1CWCcpJYOtfwsIg87P49M1CWCcpJYOl2FN29fkAjjGQo7hAJtuXK3Ej8Uld
   ```

   Start the development server with:

   ```sh
   npm run start
   ```

   - Expected Output: Vite server running at `http://localhost:5173`

##### ✅ Your Laravel backend is now set up and running!

