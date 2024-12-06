from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy.sql import text
from app.db import get_db

router = APIRouter()

@router.get("/test")
def test_endpoint(db: Session = Depends(get_db)):
    try:
        db.execute(text("SELECT 1"))  # Use text() to wrap the SQL expression
        return {"message": "API is working and database connection is successful!"}
    except Exception as e:
        return {"error": "Database connection failed!", "details": str(e)}
