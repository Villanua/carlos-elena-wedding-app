# Module 1: Project Initialization

This guide explains how to set up a project combining **Node.js (Express)** as the backend and **React (Vite)** as the frontend.

---

## 📂 1. Project Structure

Create a root directory to hold both parts of the application:

```bash
mkdir my-fullstack-app
cd my-fullstack-app
```

## 🛠️ 2. Backend Setup (Express)

Initialize the Node.js project and install Express:

```bash
# Initialize npm
npm init -y

# Install production dependencies
npm install express
```

## 💻 3. Frontend Setup (React + Vite)

Create the React application inside a `frontend` folder:

```bash
# Create Vite project
npm create vite@latest frontend -- --template react

# Install frontend dependencies
cd frontend
npm install
```

## 📄 4. Key Files Architecture

*   **Root `index.js`**: The Express server configured to serve the React production build.
*   **Root `package.json`**: Contains the main scripts for the production server.
*   **Frontend folder**: Contains the React source code and its own `package.json`.
