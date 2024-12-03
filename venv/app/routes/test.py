from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy.sql import text
from app.database import get_db

router = APIRouter()

@router.get("/")
def test_connection(db: Session = Depends(get_db)):
    """
    A basic endpoint to test if the API and database connection work.
    """
    try:
        db.execute(text("SELECT 1"))
        return {"message": "API is working and database connection is successful!"}
    except Exception as e:
        return {"error": "Database connection failed!", "details": str(e)}
