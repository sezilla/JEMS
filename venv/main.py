import uvicorn
from app.app import app

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)










































# ├── app
# │   ├── config.py
# │   ├── database.py
# │   ├── models.py
# │   ├── schemas.py
# │   ├── services
# │   │   ├── team_allocation.py
# └── main.py










# ├── app
# │   ├── __init__.py
# │   ├── app.py
# │   ├── config.py
# │   ├── db.py
# │   ├── models.py
# │   ├── schemas.py
# │   ├── services
# │   │   ├── __init__.py
# │   │   ├── allocation_service.py
# │   │   ├── team_service.py
# │   ├── utils
# │   │   ├── __init__.py
# │   │   ├── ssh_tunnel.py
# │   │   ├── logger.py
# │   └── routers
# │       ├── __init__.py
# │       ├── allocation.py
# │       ├── history.py
# │       ├── test.py
# └── main.py









# import random
# from datetime import datetime
# from sqlalchemy import create_engine, Column, Integer, String, Text, ForeignKey
# from sqlalchemy.ext.declarative import declarative_base
# from sqlalchemy.orm import sessionmaker, relationship
# from sqlalchemy.sql import text
# from fastapi import FastAPI, HTTPException, Depends
# from pydantic import BaseModel
# from sqlalchemy.orm import Session
# from sqlalchemy.exc import SQLAlchemyError
# import uvicorn
# import logging
# from logging.config import dictConfig
# import os
# from dotenv import load_dotenv

# from sqlalchemy.orm import sessionmaker
# from sshtunnel import SSHTunnelForwarder

# # Load environment variables
# DATABASE_HOST = os.getenv("DB_HOST")
# DATABASE_PORT = int(os.getenv("DB_PORT", "3306"))
# DATABASE_NAME = os.getenv("DB_DATABASE")
# DATABASE_USER = os.getenv("DB_USERNAME")
# DATABASE_PASSWORD = os.getenv("DB_PASSWORD")
# SSH_HOST = os.getenv("SSH_HOST")
# SSH_USER = os.getenv("SSH_USERNAME")
# SSH_PRIVATE_KEY_PATH = "/etc/secrets/jem"  # Replace with Render's secret file path

# # SSH Tunnel
# ssh_tunnel = SSHTunnelForwarder(
#     (SSH_HOST, 22),
#     ssh_username=SSH_USER,
#     ssh_private_key=SSH_PRIVATE_KEY_PATH,
#     remote_bind_address=(DATABASE_HOST, DATABASE_PORT),
#     local_bind_address=('127.0.0.1', 3306)
# )

# ssh_tunnel.start()

# # Database URL
# DATABASE_URL = f"mysql+pymysql://{DATABASE_USER}:{DATABASE_PASSWORD}@127.0.0.1:3306/{DATABASE_NAME}"

# # SQLAlchemy setup
# engine = create_engine(DATABASE_URL)
# SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
# Base = declarative_base()

# logging_config = {
#     "version": 1,
#     "formatters": {
#         "default": {
#             "format": "%(asctime)s - %(name)s - %(levelname)s - %(message)s",
#         },
#     },
#     "handlers": {
#         "console": {
#             "class": "logging.StreamHandler",
#             "formatter": "default",
#         },
#     },
#     "root": {
#         "level": "DEBUG",
#         "handlers": ["console"],
#     },
# }
# dictConfig(logging_config)

# logger = logging.getLogger(__name__)


# # Models
# class Package(Base):
#     __tablename__ = 'packages'
#     id = Column(Integer, primary_key=True, index=True)
#     name = Column(String, nullable=False)
#     description = Column(Text, nullable=True)


# class Task(Base):
#     __tablename__ = 'tasks'
#     id = Column(Integer, primary_key=True, index=True)
#     department_id = Column(Integer, ForeignKey('departments.id'))
#     name = Column(String, nullable=False)
#     description = Column(Text, nullable=True)


# class TaskPackage(Base):
#     __tablename__ = 'task_package'
#     task_id = Column(Integer, ForeignKey('tasks.id'), primary_key=True)
#     package_id = Column(Integer, ForeignKey('packages.id'), primary_key=True)


# class Department(Base):
#     __tablename__ = 'departments'
#     id = Column(Integer, primary_key=True, index=True)
#     name = Column(String, nullable=False)
#     description = Column(Text, nullable=True)


# class DepartmentTeam(Base):
#     __tablename__ = 'departments_has_teams'
#     department_id = Column(Integer, ForeignKey('departments.id'), primary_key=True)
#     team_id = Column(Integer, ForeignKey('teams.id'), primary_key=True)


# class Team(Base):
#     __tablename__ = 'teams'
#     id = Column(Integer, primary_key=True, index=True)
#     name = Column(String, nullable=False)
#     description = Column(Text, nullable=True)


# class Project(Base):
#     __tablename__ = 'projects'
#     id = Column(Integer, primary_key=True, index=True)
#     name = Column(String, nullable=False)


# class ProjectTeam(Base):
#     __tablename__ = 'project_teams'
#     project_id = Column(Integer, ForeignKey('projects.id'), primary_key=True)
#     team_id = Column(Integer, ForeignKey('teams.id'), primary_key=True)


# # FastAPI App
# app = FastAPI()

# @app.on_event("startup")
# def startup_event():
#     # Verify the SSH tunnel
#     if not ssh_tunnel.is_active:
#         raise RuntimeError("SSH tunnel failed to start")

# @app.on_event("shutdown")
# def shutdown_event():
#     # Stop the SSH tunnel
#     ssh_tunnel.stop()

# # Dependency
# def get_db():
#     db = SessionLocal()
#     try:
#         yield db
#     finally:
#         db.close()


# # Request Schema
# class ProjectAllocationRequest(BaseModel):
#     project_name: str
#     package_id: int
#     start: str
#     end: str


# # Allocator Class
# class EventTeamAllocator:
#     def __init__(self):
#         self.team_schedules = {}
#         self.project_history = []
#         self.allocated_teams = {}  # Store allocated teams by project name

#     def get_package_tasks(self, db, package_id):
#         tasks = db.query(TaskPackage.task_id).filter(TaskPackage.package_id == package_id).all()
#         return [task.task_id for task in tasks]

#     def get_department_for_task(self, db, task_id):
#         task = db.query(Task).filter(Task.id == task_id).first()
#         if task:
#             return task.department_id
#         raise ValueError(f"Task with ID {task_id} not found")

#     def get_teams_for_department(self, db, department_id):
#         teams = db.query(DepartmentTeam.team_id).filter(DepartmentTeam.department_id == department_id).all()
#         return [team.team_id for team in teams]

#     def is_team_available(self, team_id, start, end):
#         start_dt = datetime.strptime(start, '%Y-%m-%d')
#         end_dt = datetime.strptime(end, '%Y-%m-%d')
#         return all(end_dt < s or start_dt > e for s, e in self.team_schedules.get(team_id, []))

#     def allocate_teams(self, db, project_name, package_id, start, end):
#         start_dt = datetime.strptime(start, '%Y-%m-%d')
#         end_dt = datetime.strptime(end, '%Y-%m-%d')

#         package_tasks = self.get_package_tasks(db, package_id)
#         departments_needed = {self.get_department_for_task(db, task_id) for task_id in package_tasks}

#         allocated_teams = {}

#         for dept_id in departments_needed:
#             department_teams = self.get_teams_for_department(db, dept_id)
#             available_teams = [t for t in department_teams if self.is_team_available(t, start, end)]

#             if not available_teams:
#                 continue

#             selected_team = random.choice(available_teams)
#             allocated_teams[dept_id] = selected_team
#             self.team_schedules.setdefault(selected_team, []).append((start_dt, end_dt))

#         self.save_allocated_teams_to_laravel(db, project_name, allocated_teams)

#         result = {
#             'project_name': project_name,
#             'package_id': package_id,
#             'start': start,
#             'end': end,
#             'allocated_teams': allocated_teams
#         }
#         self.project_history.append(result)
#         self.allocated_teams[project_name] = allocated_teams  # Store allocated teams
#         return result

#     def save_allocated_teams_to_laravel(self, db, project_name, allocated_teams):
#         project = db.query(Project).filter(Project.name == project_name).first()
#         if not project:
#             raise ValueError(f"Project with name '{project_name}' not found")

#         for department_id, team_id in allocated_teams.items():
#             project_team_entry = ProjectTeam(project_id=project.id, team_id=team_id)
#             db.add(project_team_entry)
#         db.commit()



# # Global Allocator
# allocator = EventTeamAllocator()

# @app.post("/allocate-teams")
# def allocate_teams(request: ProjectAllocationRequest, db=Depends(get_db)):
#     logger.info("Received allocation request: %s", request)
#     try:
#         result = allocator.allocate_teams(
#             db,
#             request.project_name,
#             request.package_id,
#             request.start,
#             request.end
#         )
#         logger.info("Allocation result: %s", result)
#         return result
#     except Exception as e:
#         logger.error("Error during team allocation: %s", str(e))
#         raise HTTPException(status_code=400, detail=f"Error: {str(e)}")
    
# @app.get("/allocated-teams/{project_name}")
# def get_allocated_teams(project_name: str):
#     logger.info("Fetching allocated teams for project: %s", project_name)
#     if project_name in allocator.allocated_teams:
#         return allocator.allocated_teams[project_name]
#     else:
#         logger.warning("No allocated teams found for project: %s", project_name)
#         raise HTTPException(status_code=404, detail=f"No allocated teams found for project '{project_name}'")

# @app.get("/project-history")
# def get_project_history():
#     return allocator.project_history

# @app.get("/test")
# def test_endpoint(db: Session = Depends(get_db)):
#     """
#     A basic endpoint to test if the API and database connection work.
#     """
#     try:
#         # Run a basic query to test the database connection
#         db.execute(text("SELECT 1"))
#         return {"message": "API is working and database connection is successful!"}
#     except SQLAlchemyError as e:
#         return {"error": "Database connection failed!", "details": str(e)}

# # Initialize Database Tables
# Base.metadata.create_all(bind=engine)

# if __name__ == "__main__":
#     uvicorn.run(app, host="0.0.0.0", port=8000)

