# Mars Rover Mission

This repository contains a small full-stack application that simulates the commands sent to a Mars rover. A Laravel backend exposes an API for generating obstacles and executing rover commands, while a Vue 3 frontend provides a simple interface to visualize the mission.

## Project Origin
The idea for this project comes from the following mission description:

- A rover starts at an initial position `(x, y)` facing a direction `N`, `S`, `E`, or `W`.
- It receives a sequence of commands such as `FFRRFFFRL`.
- The rover can move **forward** (`F`), rotate **left** (`L`), or rotate **right** (`R`).
- The planet is represented as a square grid of `200 x 200` cells that wraps around the edges.
- Obstacles must be detected before each move. When an obstacle blocks the next step, the rover stops at the last free cell and reports the obstacle.

## Repository Structure

```
mars-rover-mission/
│
├── backend/                           # Laravel REST API
│   ├── app/
│   │   └── Http/
│   │       └── Controllers/
│   │            └── RoverController.php         # Main API controller
│   │
│   ├── app/
│   │   └── Services/
│   │         └── RoverService.php               # Core logic and obstacle generator
│   │
│   ├── routes/
│   │    └── api.php                             # Defines /api/rover/start and /execute routes
│   │
│   └── tests/
│       └── Feature/
│           ├── RoverStartTest.php              # Validates initial position and response
│           └── RoverExecuteTest.php            # Tests command execution, obstacles, collisions
│
├── frontend/                        # Vue 3 + Vite Frontend
|   └── frontend/
|       ├── public/
|       │   └── space-groove.mp3                  # Background music
|       ├── api/
|       │   └── rover.js                      # Axios API calls to backend
|       │
|       ├── src/
|       │   │
|       │   ├── components/
|       │   │   ├── AudioControl.vue             # Handles background music
|       │   │   ├── ObstacleModal.vue            # Modal for collision feedback
|       │   │   ├── RobotIdle.vue                # Displayed before start
|       │   │   ├── RoverForm.vue                # Input form: position + commands
|       │   │   ├── RoverMap.vue                 # Animated grid + rover movement
|       │   │   └── RoverResult.vue              # Displays mission outcome
|       │   │
|       │   ├── views/
|       │   │   └── MarsMission.vue              # Main page assembling all components
|       │   │
|       │   ├── tests/
|       │   │   └── RoverForm.test.js            # Unit test with Vitest
|       │   │
|       │   ├── App.vue                          # Root Vue component
|       │   ├── main.js                          # Vue app entrypoint
|       │   └── style.css                        # Tailwind styles and overrides
|       │
|       ├── index.html                           # Vite HTML template
|       └── .gitignore
│
└── README.md                                # Full project documentation


```

### Backend

The backend is built using **Laravel** and exposes a RESTful API under the `/api/rover` namespace. It handles obstacle generation, input validation, and rover movement simulation within a 200x200 Mars grid.

#### API Endpoints

Defined in `routes/api.php`:

- `POST /api/rover/start`  
  Initializes a mission based on the rover's starting position (`x`, `y`) and direction (`N`, `E`, `S`, `W`).  
  Returns a set of 800 random obstacles, ensuring:
  - The starting position is not occupied by an obstacle.
  - The rover is not completely surrounded at spawn time.

- `POST /api/rover/execute`  
  Executes a sequence of movement commands based on the initial state, obstacles, and command list.  
  Returns:
  - The step-by-step simulation (`x`, `y`, `direction`) after each command.
  - Whether a collision with an obstacle occurred.
  - The final rover state and collision coordinates (if any).

#### Core Logic

The main mission logic is implemented in `/app/Services/RoverService.php`
Responsibilities include:
- Validating movement and direction changes.
- Preventing the rover from exiting the grid bounds (0–199).
- Detecting collisions based on the provided obstacle list.
- Halting execution if a collision is detected mid-sequence.

#### Validation

Request validation is performed using Laravel form request classes to ensure:
- Input coordinates are within range (0–199).
- Valid direction values (`N`, `E`, `S`, `W`).
- Valid commands (`F`, `L`, `R`).
- Properly structured obstacle arrays (`[[x1, y1], [x2, y2], ...]`).

#### Tests
Feature tests are provided for both endpoints and are located in:
```
tests/Feature/
├── RoverStartTest.php
└── RoverExecuteTest.php
```
These tests verify:
- Correct obstacle generation behavior.
- Accurate movement logic and command execution.
- Proper handling of invalid input and collision scenarios.

  
### Frontend
The frontend folder contains a Vite-powered Vue 3 application. It communicates with the backend API to start the mission and display the rover steps on a map.

#### RoverForm.vue  
Renders a user-friendly form to input initial rover data (coordinates, direction, commands). On submission, it initializes the mission by calling the backend.

#### RoverMap.vue  
A camera follows the rover's position using a downscaled 11x11 animated viewport simulating it's viewing range. Only the rover and visible obstacles are rendered. The animation progresses one step every 500ms, stopping at mission completion or at last before hitting an obstacle.

#### RoverResult.vue  
Shows the final state of the mission including whether it was successful or interrupted by a collision. Also displays position, direction, and obstacle details.

#### AudioControl.vue  
Toggles background music playback (included as space-groove.mp3) and also includes a slider for volume level.

#### ObstacleModal.vue  
Modal that interrupts the mission *before* a collision occurs, warning that the next step would result in hitting an obstacle. It displays the obstacle position, mission status ("Stopped"), and a safety message. The rover halts proactively to avoid impact.

#### RobotIdle.vue  
Used to show the rover in idle state before mission begins.

#### State Persistence
The mission state — including the rover's last known position, final orientation, and obstacle layout — is automatically stored in `localStorage` to ensure session consistency.

- This allows users to resume their mission even after a page reload.
- Pressing the **"Clear"** button will fully reset the mission by:
  - Removing all saved data from `localStorage`
  - Resetting the rover to an idle state

## Getting Started

### Backend Setup
1. Install dependencies:
   ```bash
   cd backend
   composer install
   ```
2. Copy `.env.example` to `.env` and set the application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Run the development server:
   ```bash
   php artisan serve
   ```
4. Execute the test suite (optional):
   ```bash
   php artisan test
   ```
For additional testing details see `docs/TESTING.md`.

### Frontend Setup
1. Install dependencies:
   ```bash
   cd frontend/frontend
   npm install
   ```
2. Start the frontend development server:
   ```bash
     npm run dev
   ```

4. Once running, open the app in your browser at:
   **[http://localhost:5173](http://localhost:5173)**  
   (or the port shown in your terminal if different).
The frontend expects the backend to be running at `http://localhost:8000`. Adjust the API base URL in `frontend/frontend/api/rover.js` if needed.


## License
This project is delivered as part of a technical challenge. No specific license is applied.
