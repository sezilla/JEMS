import random
from datetime import datetime
from sqlalchemy import create_engine, Column, Integer, String, Text, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship
from sqlalchemy.sql import text
from fastapi import FastAPI, HTTPException, Depends
from pydantic import BaseModel
from sqlalchemy.orm import Session
from sqlalchemy.exc import SQLAlchemyError
import uvicorn
import logging
from logging.config import dictConfig
import os
from dotenv import load_dotenv
from sshtunnel import SSHTunnelForwarder

# Load environment variables
# load_dotenv('.env')

# # Database Setup
# DATABASE_URL = os.getenv("DB_CONNECTION", "mysql") + "+pymysql://" + \
#                os.getenv("DB_USERNAME", "root") + ":" + \
#                os.getenv("DB_PASSWORD", "") + "@" + \
#                os.getenv("DB_HOST", "localhost") + ":" + \
#                os.getenv("DB_PORT", "3306") + "/" + \
#                os.getenv("DB_DATABASE", "your_database_name")

# engine = create_engine(DATABASE_URL)
# SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
# Base = declarative_base()



DB_CONNECTION = "mysql"
DB_USERNAME = "forge"
DB_PASSWORD = "yvATyxksz6VG3ElvXP5x"
DB_HOST = "127.0.0.1"
DB_PORT = "3306"
DB_DATABASE = "forge"
SSH_HOST = "54.151.162.229"
SSH_USERNAME = "forge"
PRIVATE_KEY = """-----BEGIN OPENSSH PRIVATE KEY-----
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
-----END OPENSSH PRIVATE KEY-----"""


if not PRIVATE_KEY:
    raise ValueError("Private key not found in environment variables")

with SSHTunnelForwarder(
    (SSH_HOST, 22),
    ssh_username=SSH_USERNAME,
    ssh_pkey=PRIVATE_KEY,
    remote_bind_address=(DB_HOST, int(DB_PORT))
) as tunnel:
    # After the tunnel is created, we can connect to MySQL via the tunnel
    DATABASE_URL = f"mysql+pymysql://{DB_USERNAME}:{DB_PASSWORD}@127.0.0.1:{tunnel.local_bind_port}/{DB_DATABASE}"

    engine = create_engine(DATABASE_URL)
    SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

    # Now you can proceed with your database operations
    Base.metadata.create_all(bind=engine)




logging_config = {
    "version": 1,
    "formatters": {
        "default": {
            "format": "%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        },
    },
    "handlers": {
        "console": {
            "class": "logging.StreamHandler",
            "formatter": "default",
        },
    },
    "root": {
        "level": "DEBUG",
        "handlers": ["console"],
    },
}
dictConfig(logging_config)

logger = logging.getLogger(__name__)


# Models
class Package(Base):
    __tablename__ = 'packages'
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, nullable=False)
    description = Column(Text, nullable=True)


class Task(Base):
    __tablename__ = 'tasks'
    id = Column(Integer, primary_key=True, index=True)
    department_id = Column(Integer, ForeignKey('departments.id'))
    name = Column(String, nullable=False)
    description = Column(Text, nullable=True)


class TaskPackage(Base):
    __tablename__ = 'task_package'
    task_id = Column(Integer, ForeignKey('tasks.id'), primary_key=True)
    package_id = Column(Integer, ForeignKey('packages.id'), primary_key=True)


class Department(Base):
    __tablename__ = 'departments'
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, nullable=False)
    description = Column(Text, nullable=True)


class DepartmentTeam(Base):
    __tablename__ = 'departments_has_teams'
    department_id = Column(Integer, ForeignKey('departments.id'), primary_key=True)
    team_id = Column(Integer, ForeignKey('teams.id'), primary_key=True)


class Team(Base):
    __tablename__ = 'teams'
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, nullable=False)
    description = Column(Text, nullable=True)


class Project(Base):
    __tablename__ = 'projects'
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, nullable=False)


class ProjectTeam(Base):
    __tablename__ = 'project_teams'
    project_id = Column(Integer, ForeignKey('projects.id'), primary_key=True)
    team_id = Column(Integer, ForeignKey('teams.id'), primary_key=True)


# FastAPI App
app = FastAPI()

# Dependency
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


# Request Schema
class ProjectAllocationRequest(BaseModel):
    project_name: str
    package_id: int
    start: str
    end: str


# Allocator Class
class EventTeamAllocator:
    def __init__(self):
        self.team_schedules = {}
        self.project_history = []
        self.allocated_teams = {}  # Store allocated teams by project name

    def get_package_tasks(self, db, package_id):
        tasks = db.query(TaskPackage.task_id).filter(TaskPackage.package_id == package_id).all()
        return [task.task_id for task in tasks]

    def get_department_for_task(self, db, task_id):
        task = db.query(Task).filter(Task.id == task_id).first()
        if task:
            return task.department_id
        raise ValueError(f"Task with ID {task_id} not found")

    def get_teams_for_department(self, db, department_id):
        teams = db.query(DepartmentTeam.team_id).filter(DepartmentTeam.department_id == department_id).all()
        return [team.team_id for team in teams]

    def is_team_available(self, team_id, start, end):
        start_dt = datetime.strptime(start, '%Y-%m-%d')
        end_dt = datetime.strptime(end, '%Y-%m-%d')
        return all(end_dt < s or start_dt > e for s, e in self.team_schedules.get(team_id, []))

    def allocate_teams(self, db, project_name, package_id, start, end):
        start_dt = datetime.strptime(start, '%Y-%m-%d')
        end_dt = datetime.strptime(end, '%Y-%m-%d')

        package_tasks = self.get_package_tasks(db, package_id)
        departments_needed = {self.get_department_for_task(db, task_id) for task_id in package_tasks}

        allocated_teams = {}

        for dept_id in departments_needed:
            department_teams = self.get_teams_for_department(db, dept_id)
            available_teams = [t for t in department_teams if self.is_team_available(t, start, end)]

            if not available_teams:
                continue

            selected_team = random.choice(available_teams)
            allocated_teams[dept_id] = selected_team
            self.team_schedules.setdefault(selected_team, []).append((start_dt, end_dt))

        self.save_allocated_teams_to_laravel(db, project_name, allocated_teams)

        result = {
            'project_name': project_name,
            'package_id': package_id,
            'start': start,
            'end': end,
            'allocated_teams': allocated_teams
        }
        self.project_history.append(result)
        self.allocated_teams[project_name] = allocated_teams  # Store allocated teams
        return result

    def save_allocated_teams_to_laravel(self, db, project_name, allocated_teams):
        project = db.query(Project).filter(Project.name == project_name).first()
        if not project:
            raise ValueError(f"Project with name '{project_name}' not found")

        for department_id, team_id in allocated_teams.items():
            project_team_entry = ProjectTeam(project_id=project.id, team_id=team_id)
            db.add(project_team_entry)
        db.commit()



# Global Allocator
allocator = EventTeamAllocator()

@app.post("/allocate-teams")
def allocate_teams(request: ProjectAllocationRequest, db=Depends(get_db)):
    logger.info("Received allocation request: %s", request)
    try:
        result = allocator.allocate_teams(
            db,
            request.project_name,
            request.package_id,
            request.start,
            request.end
        )
        logger.info("Allocation result: %s", result)
        return result
    except Exception as e:
        logger.error("Error during team allocation: %s", str(e))
        raise HTTPException(status_code=400, detail=f"Error: {str(e)}")
    
@app.get("/allocated-teams/{project_name}")
def get_allocated_teams(project_name: str):
    logger.info("Fetching allocated teams for project: %s", project_name)
    if project_name in allocator.allocated_teams:
        return allocator.allocated_teams[project_name]
    else:
        logger.warning("No allocated teams found for project: %s", project_name)
        raise HTTPException(status_code=404, detail=f"No allocated teams found for project '{project_name}'")

@app.get("/project-history")
def get_project_history():
    return allocator.project_history

@app.get("/test")
def test_endpoint(db: Session = Depends(get_db)):
    """
    A basic endpoint to test if the API and database connection work.
    """
    try:
        # Run a basic query to test the database connection
        db.execute(text("SELECT 1"))
        return {"message": "API is working and database connection is successful!"}
    except SQLAlchemyError as e:
        return {"error": "Database connection failed!", "details": str(e)}

# Initialize Database Tables
Base.metadata.create_all(bind=engine)

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
