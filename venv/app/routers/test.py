from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.db import get_db

router = APIRouter()

@router.get("/test")
def test_endpoint(db: Session = Depends(get_db)):
    try:
        db.execute("SELECT 1")
        return {"message": "API is working and database connection is successful!"}
    except Exception as e:
        return {"error": "Database connection failed!", "details": str(e)}
