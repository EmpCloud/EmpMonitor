# EmpMonitor-OpenSource Docker Setup Guide

This guide explains how to use Docker and `docker-compose` to set up and run the EmpMonitor-OpenSource project. The provided `docker-compose.yml` orchestrates all required services for a full development or production environment.

---

## Table of Contents
- [Overview](#overview)
- [Services](#services)
- [Prerequisites](#prerequisites)
- [Setup Instructions](#setup-instructions)
- [Environment Variables](#environment-variables)
- [Volumes & Data Persistence](#volumes--data-persistence)
- [Common Commands](#common-commands)
- [Troubleshooting](#troubleshooting)

---

## Overview

The `docker-compose.yml` file defines the following services:
- MariaDB (MySQL-compatible database)
- MongoDB (NoSQL database)
- Four Node.js backend services: Main, Agent, Report, Store
- Laravel-based Frontend (PHP)
- Redis (optional, for caching)

All services are connected via a shared Docker network for seamless communication.

---

## Services

| Service         | Description                                 | Ports Exposed |
|-----------------|---------------------------------------------|---------------|
| mariadb         | MySQL-compatible database                   | 3306          |
| mongodb         | NoSQL database                              | 27017         |
| backend-main    | Main Node.js backend API                    | 5001          |
| backend-agent   | Agent Node.js backend API                   | 5002          |
| backend-report  | Report Node.js backend API                  | 5003          |
| backend-store   | Store Node.js backend API                   | 5004          |
| frontend        | Laravel PHP frontend                        | 8000          |
| redis           | Redis cache (optional, for Laravel)         | 6379          |

---

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (v20+ recommended)
- [Docker Compose](https://docs.docker.com/compose/) (v2+ recommended)

---

## Setup Instructions

1. **Clone the repository:**
   ```sh
   git clone <repo-url>
   cd EmpMonitor-OpenSource
   ```

2. **(Optional) Adjust environment variables:**
   - Review and edit environment variables in `docker-compose.yml` as needed.
   - For the frontend, you may copy and adjust `Frontend/sample_docker.env` if required.

3. **Build and start all services:**
   ```sh
   docker-compose up --build
   ```
   This will build images (if not already built) and start all containers.

4. **Access the application:**
   - **Frontend:** http://localhost:8000
   - **APIs:**
     - Main: http://localhost:5001
     - Agent: http://localhost:5002
     - Report: http://localhost:5003
     - Store: http://localhost:5004
   - **Databases:**
     - MariaDB: localhost:3306 (user: empmonitor, password: empmonitor123)
     - MongoDB: localhost:27017
     - Redis: localhost:6379

---

## Environment Variables

Each service defines its own environment variables in `docker-compose.yml`. Key variables include database credentials, JWT secrets, and service URLs. Adjust these as needed for your environment.

---

## Volumes & Data Persistence

- **MariaDB:** Data stored in `mariadb_data` volume (persistent)
- **MongoDB:** Data stored in `mongodb_data` volume (persistent)
- **Redis:** Data stored in `redis_data` volume (persistent)
- **Source Code:** Each backend/frontend service mounts its source directory for live code updates (except for `node_modules` and `vendor` which are managed inside containers)

---

## Common Commands

- **Start all services:**
  ```sh
  docker-compose up
  ```
- **Rebuild images:**
  ```sh
  docker-compose up --build
  ```
- **Stop all services:**
  ```sh
  docker-compose down
  ```
- **View logs:**
  ```sh
  docker-compose logs -f
  ```
- **Access a container shell:**
  ```sh
  docker exec -it <container_name> /bin/sh
  ```

---

## Troubleshooting

- **Port Conflicts:** Ensure ports 3306, 27017, 5001-5004, 8000, and 6379 are free.
- **Permission Issues:** On Windows, you may need to run Docker as administrator.
- **Database Initialization:** MariaDB is initialized with `Backend/Database_Configuration/db.sql`.
- **Frontend Issues:** If you see Laravel errors, ensure dependencies are installed and the `.env` file is configured.
- **Node Modules:** Node.js services use a volume for `/app/node_modules` to avoid host/OS conflicts.

---

## Additional Notes

- For production, review and secure all secrets and credentials.
- You can customize service configurations by editing the respective Dockerfiles and environment variables.
- For more details, see the individual `README.md` files in each service directory.

---

Happy Monitoring! 