from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import Project

router = APIRouter()

@router.get("/")
def get_projects(db: Session = Depends(get_db)):
    """
    Retrieve all projects from the database.
    """
    try:
        projects = db.query(Project).all()
        return projects
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error retrieving projects: {str(e)}")

@router.get("/{project_id}")
def get_project(project_id: int, db: Session = Depends(get_db)):
    """
    Retrieve a specific project by ID.
    """
    project = db.query(Project).filter(Project.id == project_id).first()
    if not project:
        raise HTTPException(status_code=404, detail="Project not found")
    return project
