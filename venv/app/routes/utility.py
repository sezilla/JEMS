from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy.exc import SQLAlchemyError
from sqlalchemy.sql import text
from app.database import get_db

router = APIRouter()

@router.get("/project-history")
def get_project_history():
    return allocator.project_history

@router.get("/test")
def test_endpoint(db: Session = Depends(get_db)):
    try:
        db.execute(text("SELECT 1"))
        return {"message": "API is working and database connection is successful!"}
    except SQLAlchemyError as e:
        return {"error": "Database connection failed!", "details": str(e)}
