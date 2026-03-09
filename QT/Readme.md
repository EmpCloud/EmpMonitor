<div align="right">
  <img src="https://img.shields.io/badge/OpenSource-000?style=for-the-badge&logo=ghost&logoColor=black&color=ffd700" alt="OpenSource-Badge">
</div>

![EmpMonitor](/assets/EMPMonitor%20logo.png)
<p align="center"><i>Your Workforce Productivity Compass</i></p>

> **_EmpMonitor: The Ultimate Solution for Workforce Monitoring and Productivity Optimization_**

# ➤ QT Service

### What is QT?
**Qt** is a powerful cross-platform framework used for developing graphical user interfaces (**GUIs**) and applications that run on Windows, macOS, Linux, and mobile devices. It provides tools for creating sleek, responsive interfaces while handling low-level system operations. The framework is written in **C++** but supports other languages like Python and JavaScript, making it versatile for different development needs.

This project specifically uses **Qt 5.15 Open Source Edition**, which is free for non-commercial use. Qt simplifies tasks like drawing UI elements, managing user inputs, and connecting to databases, allowing developers to focus on functionality rather than platform-specific details. Its modular design means you only include what your project requires, keeping applications lightweight.

---

## ➤ Installation

1. **Install the prerequisites:**
    - **[C++14 Installation](#step-1-c14-installation)**
    - **[Qt 5.15 (open source) Installation](#step-2-qt-515-open-source-installation)**
    - **[Microsoft Visual Studio Community Edition 2019](#step-3-microsoft-visual-studio-community-edition-2019)**
    - **[Microsoft Visual C++ Redistributable](#step-4-microsoft-visual-c-redistributable)**

---

## ➤ Prerequisites
Before you can build and run the project, you need to ensure that the following software and tools are installed:

### Step 1: C++14 installation
#### Windows: 
- Make sure your development environment supports C++14. The project is built with C++14 features, and using a compiler that supports this standard is required for successful compilation.
    - To check if your compiler supports C++14, run the following command:
     ```sh
        g++ --version
    ```
#### macOS: 
- You can use Xcode or install a C++ compiler like GCC or Clang that supports C++14.
- Install Homebrew (if not already installed):
    ```sh
        /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    ```
- Install GCC:
    ```sh
        brew install gcc

        # To verify the installation
        gcc --version
    ```
#### Linux:
- You can install the necessary packages with your package manager.
- On Ubuntu or Debian-based systems, use apt to install GCC:
    ```sh
    sudo apt update
    sudo apt install gcc g++

    # To verify the installation, run the following command
    gcc --version
    ```

   


### Step 2: Qt 5.15 (Open Source) Installation
This project requires **Qt 5.15**, a cross-platform framework used for developing GUI applications. Ensure you install the open-source version to avoid licensing issues.
- [Download](https://www.qt.io/download) the **Qt 5.15 Open Source Edition** from the official Qt website.
- To install **Qt 5.15** on Windows, start by downloading the Qt Online Installer from the official website. 
- Once downloaded, run the installer and sign in with your Qt account or create a free one if you don’t have one. 
- During installation, select Custom Installation and choose Qt 5.15.x from the available versions. 
- If you are using **MinGW**, ensure that the **MinGW-w64** option is checked, or select **MSVC** if you are using Microsoft Visual Studio. 
- After completing the installation, restart your terminal to apply the changes.

<!-- #### Windows: 
- To install **Qt 5.15** on Windows, start by downloading the Qt Online Installer from the [Official Qt website](https://www.qt.io/download).
- Once downloaded, run the installer and sign in with your Qt account or create a free one if you don’t have one. 
- During installation, select Custom Installation and choose Qt 5.15.x from the available versions. 
- If you are using MinGW, ensure that the MinGW-w64 option is checked, or select MSVC if you are using Microsoft Visual Studio. 
- After completing the installation, restart your terminal to apply the changes.
- To verify, run:
    ```sh
        qmake --version
    ```
####  macOS (Using Homebrew):
- If you’re on macOS, you can install Qt 5.15 using Homebrew:
    ```sh
        brew install qt@5
    ```
- After installation, add Qt to your PATH:
    ```sh
        echo 'export PATH="/usr/local/opt/qt@5/bin:$PATH"' >> ~/.zshrc
        source ~/.zshrc
    ```
- verify the installation by running:
    ```sh
        qmake --version
    ```

####  Linux (Ubuntu/Debian):
- On Ubuntu or Debian-based systems, you can install Qt 5.15 using the package manager:
    ```sh
        sudo apt update
        sudo apt install qt5-default
    ```
- For manual installation, download the Qt installer from: [Official Qt website](https://www.qt.io/download). Follow the installation instructions for your Linux distribution.
- Verify the installation by running:
    ```sh
        qmake --version
    ``` -->


### Step 3: Microsoft Visual Studio Community Edition 2019
#### Why You Need This
Visual Studio 2019 is the primary development environment for building this project on Windows. Think of it as the "workshop" where all the coding tools come together. The Community Edition is completely free for individual developers and open-source projects.

#### Key Features You'll Get
- **MSVC Compiler**: The core tool that translates C++ code into executable programs.
- **Debugging Tools**: Helps find and fix errors in your code.
- **Qt Integration**: Works seamlessly with Qt Creator for GUI development.
- **Project Management**: Keeps all your code files organized.

For compiling and building the project, you will need Microsoft Visual Studio 2019. The Community Edition is available for free and can be downloaded from the [Microsoft website](https://visualstudio.microsoft.com/visual-cpp-build-tools/).

Make sure to install the **Desktop development with C++** workload during the installation of Visual Studio to ensure you have all the necessary components, such as the MSVC C++ compiler.

<!-- After installation: 
- Open **Developer Command Prompt** 
- Type:
    ```bash
        cl/?
    ```
You should see Microsoft C++ compiler version information. -->

### Step 4: Microsoft Visual C++ Redistributable
To ensure that all necessary runtime components are available for the project, you need to install the **Microsoft Visual C++ Redistributable**. This is required to run applications built with Microsoft Visual C++.

You can download the latest version of the Microsoft Visual C++ Redistributable from the following link:

- [Download vc_redist.x64.exe](https://aka.ms/vs/17/release/vc_redist.x64.exe)

Run the downloaded installer and follow the on-screen instructions to complete the installation.

---



## ➤ Building the Application

1. **Open the `emp.pro` File:**
   The project is built using the `emp.pro` file, located at `./workspaces/emp_qt/emp.pro`. This is the primary project file which will also include the `real_time_tracker.pro` file for building the related executable.

2. **Build the Project:**
   Open the `emp.pro` file with Qt Creator or the Qt command line tool and trigger the build process. If new projects are added in the future, they should be referenced from within the `emp.pro` file.

   During the build process, the executables for `real_time_tracker.pro` will be created and saved in the `./sys/win_x64/bin` folder.

3. **Prepare Executable for Deployment:**
   To package the application with the respective `.dll` files for deployment, run the `prepare_2_windows_packages.bat` file. This is a master batch file located in the `./scripts` directory, and it will call the necessary batch files for the Qt command line build process.

   After running this batch file, a `./deploy/win_x64/empmonitor_windows_x64/gui/` folder will be created. This folder will contain the executable `empmonitor.exe` along with all the required `.dll` files.

---

## ➤ Running the Application

1. **Configure API Endpoints:**
   Before running the `empmonitor.exe`, you'll need to configure the API endpoints that define the working parameters of the application.

   To do this, change the configuration parameters in the `config.js` file located in the `./deploy/win_x64/empmonitor_windows_x64/gui/configs/` folder.
- [Download EmpMonitor Executable](https://drive.google.com/file/d/1mn_QYpzjswAaI_pZBHHb4Uh5YAQrQTCq/view?usp=drive_link)

2. **Run the Executable:**
   After configuring the `config.js` file, you can run the `empmonitor.exe` located in `./deploy/win_x64/empmonitor_windows_x64/gui/`.

   A login popup will appear. Enter the credentials to log in. The login credentials can be obtained from the admin, as they have access to the admin dashboard.














<!--  -->


