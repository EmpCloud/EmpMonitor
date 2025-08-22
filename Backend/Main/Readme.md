<div align="right">
  <img src="https://img.shields.io/badge/OpenSource-000?style=for-the-badge&logo=ghost&logoColor=black&color=ffd700" alt="OpenSource-Badge">
</div>

![EmpMonitor](/assets/EMPMonitor%20logo.png)
<p align="center"><i>Your Workforce Productivity Compass</i></p>

> **_EmpMonitor: The Ultimate Solution for Workforce Monitoring and Productivity Optimization_**




# 🖥️ Main Service
The **Main Service** is the core part of the [EmpMonitor platform](https://www.empmonitor.com/). It connects what users see and do on the front end with the complex work happening in the back end. Its main job is to make sure everything works smoothly. When users click on something or ask for information, they get the right data quickly and without problems. This service is built to keep the system organized, safe, and able to grow, ensuring that every action users take is fast and easy to understand.


The Main Service has a few important functions. First, it provides Frontend APIs, which help the user interface get and show important information like reports and activity logs. Second, it processes User Analytics & Reporting, turning raw data into useful insights that help administrators see how productive their team is and spot trends. Lastly, it manages Authentication & Access Control, making sure that only the right people, like admins and employees, can access certain parts of the dashboard based on their permissions. This way, the Main Service keeps everything running well and securely.



#### Core functionalities of the Main Service:
1. **Frontend APIs** - Provides the interface for the frontend to request and display data (e.g., reports, activity logs).
2. **User Analytics & Reporting** - Processes raw data into meaningful insights, helping admins track productivity and trends.
3. **Authentication & Access Control** - Verifies user identities (admins vs. employees) and restricts dashboard access based on permissions.


> [!NOTE]
>
> The Main Service is vital for ensuring seamless communication between users and the platform's backend. It enhances user experience by providing quick access to information and maintaining system organization, security, and scalability for effective operations.


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
- **User Interface Connection**: The Main Service acts as the bridge between the user interface and the backend processes, ensuring that users receive the correct data quickly and efficiently.
- **Data Retrieval**: It provides Frontend APIs that allow the user interface to request and display essential information, such as reports and activity logs.
- **Analytics Processing**: The service transforms raw data into actionable insights, enabling administrators to monitor team productivity and identify trends.
- **Access Management**: It manages user authentication and access control, ensuring that only authorized personnel can access specific areas of the dashboard.


## ➤ How it works?
- **Data Interaction**: When users interact with the platform, the Main Service processes their requests and retrieves the necessary data from the backend.
- **API Functionality**: The Frontend APIs facilitate communication between the user interface and the backend, allowing for seamless data display and interaction.
- **Insight Generation**: The service analyzes raw data to produce meaningful reports and analytics, which are then made available to administrators for review.
- **Security Measures**: It verifies user identities and enforces access restrictions based on roles, ensuring that sensitive information is only accessible to authorized users.


## ➤ Installation Guide
For a comprehensive, step-by-step installation process applicable to all our backend modules, please refer to our [Installation Guide](/Backend/Installation.md).

#### The guide covers:
- System requirements
- Dependency installations
- Database configurations
- Project setup steps

<!-- ##  Installation Guide

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
 
## ➤ Want to Contribute?
Contributions are always appreciated! Please refer to the [README](/README.md) for detailed instructions on [how to contribute](/Contributions.md).
