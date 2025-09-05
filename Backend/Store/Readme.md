<div align="right">
  <img src="https://img.shields.io/badge/OpenSource-000?style=for-the-badge&logo=ghost&logoColor=black&color=ffd700" alt="OpenSource-Badge">
</div>

![EmpMonitor](/assets/EMPMonitor%20logo.png)
<p align="center"><i>Your Workforce Productivity Compass</i></p>

> **_EmpMonitor: The Ultimate Solution for Workforce Monitoring and Productivity Optimization_**

# 🗃️ Store Service
The **Store Service** is an important part of the EmpMonitor platform that focuses on managing user activity data. Its main job is to securely store and check the information collected from users, such as their interactions with websites and apps. This service is crucial for ensuring that the data is reliable and can be used to understand user behavior.

One of the key functions of the Store Service is to receive user activity data, including web and app usage, from agents. It carefully examines this data to ensure it is accurate and free from duplicate entries. By validating the incoming information, the Store Service helps maintain high data quality, which is essential for any analysis that follows.

Additionally, the Store Service creates attendance records to track user engagement over time. After organizing and finalizing the data, it forwards this information to the Report Service for further processing. This smooth transfer of validated data ensures that administrators have access to accurate reports, allowing them to make informed decisions based on user activity.

> [!NOTE]
> 
> The Store Service efficiently manages and verifies user activity data, ensuring accurate information is sent to the Report Service for effective decision-making.

## ➤ Pre-Requisites
Before you start, ensure you have the following:




| 🛠️ Requirement | 📌 Version |
|--------------|------------|
| <img src="https://img.icons8.com/color/48/000000/nodejs.png" width="48"> **Node.js** | `^14 or Latest` |
| <img src="https://img.icons8.com/color/48/000000/npm.png" width="48"> **NPM** | `^7 or Latest` |
| <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUM2wJCaDfAMJDo0R2GpmgvEHsPvl-JuSrCKcTzJ66geu9AjWwVE2C0lpXUXGBXRYnt2k&usqp=CAU" width="48"> **Redis** | `For asset bundling` |
| <img src="https://media2.dev.to/dynamic/image/width=1000,height=420,fit=cover,gravity=auto,format=auto/https%3A%2F%2Fdev-to-uploads.s3.amazonaws.com%2Fuploads%2Farticles%2F3xv859acwhz1d5wv0knl.png" width="48"> **MongoDB** | `For asset bundling` |
| <img src="https://avatars.githubusercontent.com/u/5877084?s=280&v=4" width="48"> **MariaDB** | `For asset bundling` |

> [!WARNING]
>
> Properly configure and scale your systems to prevent performance issues and data transmission delays.


## ➤ What does it do?
- **Data Reception**: The Store Service gathers user activity data from various agents, capturing details about web and app usage.
- **Quality Assurance**: It verifies the accuracy of the incoming data, ensuring that it is complete and free from duplicates.
- **Attendance Tracking**: The service generates attendance records to monitor user engagement over time.
- **Data Forwarding**: After processing, it sends the validated data to the Report Service for analysis and reporting.

## ➤ How it Works?
- **Data Collection**: Agents installed on user devices collect activity data and transmit it to the Store Service.
- **Validation Process**: Upon receiving the data, the Store Service checks for accuracy and duplicates, ensuring high-quality information.
- **Record Creation**: It creates attendance records based on the validated data to track user engagement.
- **Data Transfer**: Finally, the organized data is forwarded to the Report Service, enabling comprehensive reporting and informed decision-making.

<!-- ##  Installation Process

This guide will walk you through the installation process step by step, designed for users with minimal technical background. By following these instructions carefully, you'll be able to set up the software independently.


### Step 1: Requirement Check  
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


#### 2. NPM (Node Package Manager)
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
<!-- Step 2 begins from here 
### Step 2: Set Up NodeJS Project

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

<!-- step 3 begins here 
### Step 3: Install Dependencies
#### 1. Backend Dependencies
Dependencies are external packages required for the software to function correctly. To install all necessary dependencies, use the following command:
   ```sh
      npm install
   ```
> [!TIP]
>
> This step might take a few minutes. Ensure you have a stable internet connection.




---
<!-- Step 4 begins here 
### Step 4: Generate Application Key
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
 <!-- Step 5 starts here 
 ### Step 5: Configure the Database
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

---
### Step 6: Compile Backend Assets
- To bundle application assets and start the development server, use the command below:
   ```sh
      npm run start:dev
   ```
   - Expected Output: Vite server running at `http://localhost:5173`

##### ✅ Your Laravel backend is now set up and running!  -->

## ➤ Installation Guide
For a comprehensive, step-by-step installation process applicable to all our backend modules, please refer to our [Installation Guide](/Backend/Installation.md).

#### The guide covers:
- System requirements
- Dependency installations
- Database configurations
- Project setup steps


## ➤ Want to Contribute?
Contributions are always appreciated! Please refer to the [README](/README.md) for detailed instructions on [how to contribute](/Contributions.md).





<!-- ## 🎯 Installation Process

1️⃣ **Requirement Check**  
2️⃣ **Set Up NodeJS Project**  
3️⃣ **Install Dependencies**  
4️⃣ **Generate Application Key**  
5️⃣ **Run Development Server**  
6️⃣ **Compile Assets**

---

## ✅ Step 1: Requirement Check

### 🔍 Checking Installed Versions

#### ✅ NodeJS
```sh
node -v
```
_Expected output:_ `NodeJS 14.x or later`

➡️ [Download NodeJS](https://www.php.net/downloads)

#### ✅ NPM
```sh
npm -v
```
_Expected output:_ `NPM 7.x.x or later`

➡️ [Download Node.js](https://nodejs.org/)

#### ✅ Redis
```sh
redis-server --version
```
_Expected output:_ Redis version information

➡️ [Download Redis](https://redis.io/download)

#### ✅ MongoDB
```sh
mongod --version
```
_Expected output:_ MongoDB version information

➡️ [Download MongoDB](https://www.mongodb.com/try/download/community)

#### ✅ MariaDB
```sh
mysql --version
```
_Expected output:_ MariaDB version information

➡️ [Download MariaDB](https://mariadb.org/download/)

---

## 📦 Step 4: Install NPM Dependencies for Backend

### 🔹 Install NPM Dependencies for Backend
```sh
npm install
```

---

## 🖥️ Step 6: Configure Database

1. **MariaDB**:  
   - Ensure that MariaDB is installed and running.
   - Set up a new database for your application:

   ```sh
   mysql -u root -p
   CREATE DATABASE empmonitor;
   EXIT;
   ```

2. **MongoDB**:  
   - Ensure MongoDB is installed and running.
   - Create a new MongoDB database (Laravel will handle collections automatically).

   ```sh
   mongo
   use empmonitor;
   exit;
   ```

3. **Redis**:  
   - Redis should be running as a service. You can check if it's running with:

   ```sh
   redis-cli ping
   ```

   - If Redis is not running, start it with:

   ```sh
   redis-server
   ```

---

## 🎨 Step 7: Compile Backend Assets

### 🔹 Run Node for Asset Bundling
```sh
npm run start:dev
```
_Expected output:_ `Vite server running at http://localhost:5173`

✅ Your Laravel backend is now set up and running! 🚀 -->
