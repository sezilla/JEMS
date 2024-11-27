import random
from datetime import datetime
from sqlalchemy import create_engine, Column, Integer, String, Text, ForeignKey, select
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn
import os
from dotenv import load_dotenv

# Load environment variables from Laravel's .env file
load_dotenv('.env')  # Ensure your Laravel .env is in the same directory or provide the correct path

# Database Setup: Use values from the .env file
DATABASE_URL = os.getenv("DB_CONNECTION", "mysql") + "+pymysql://" + \
              os.getenv("DB_USERNAME", "root") + ":" + \
              os.getenv("DB_PASSWORD", "") + "@" + \
              os.getenv("DB_HOST", "localhost") + ":" + \
              os.getenv("DB_PORT", "3306") + "/" + \
              os.getenv("DB_DATABASE", "your_database_name")

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Models
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

class Task(Base):
    __tablename__ = 'tasks'
    id = Column(Integer, primary_key=True, index=True)
    department_id = Column(Integer, ForeignKey('departments.id'))
    name = Column(String, nullable=False)
    description = Column(Text, nullable=True)

class TaskPackage(Base):
    __tablename__ = 'task_package'
    id = Column(Integer, primary_key=True, index=True)
    task_id = Column(Integer, ForeignKey('tasks.id'))
    package_id = Column(Integer, ForeignKey('packages.id'))

class Package(Base):
    __tablename__ = 'packages'
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, nullable=False)
    description = Column(Text, nullable=True)

# FastAPI App
app = FastAPI()

class ProjectAllocationRequest(BaseModel):
    project_name: str
    package_id: int
    start: str
    end: str

class EventTeamAllocator:
    def __init__(self):
        self.team_schedules = {}
        self.project_history = []

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
        team_probabilities = {}

        for dept_id in departments_needed:
            department_teams = self.get_teams_for_department(db, dept_id)
            available_teams = [t for t in department_teams if self.is_team_available(t, start, end)]

            if not available_teams:
                continue

            probs = {t: random.uniform(0.6, 1.0) for t in available_teams}
            selected_team = max(probs.items(), key=lambda x: x[1])
            allocated_teams[dept_id] = selected_team[0]
            team_probabilities[selected_team[0]] = selected_team[1]
            self.team_schedules.setdefault(selected_team[0], []).append((start_dt, end_dt))

        result = {
            'project_name': project_name,
            'package_id': package_id,
            'start': start,
            'end': end,
            'allocated_teams': allocated_teams,
            'team_probabilities': team_probabilities
        }
        self.project_history.append(result)
        return result

# Create global allocator instance
allocator = EventTeamAllocator()

# API Endpoints
@app.post("/allocate-teams")
async def allocate_teams(request: ProjectAllocationRequest):
    db = SessionLocal()
    try:
        result = allocator.allocate_teams(
            db,
            request.project_name, 
            request.package_id, 
            request.start, 
            request.end
        )
        return result
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))
    finally:
        db.close()

@app.get("/project-history")
async def get_project_history():
    return allocator.project_history

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
