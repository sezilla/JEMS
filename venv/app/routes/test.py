from fastapi import APIRouter, Depends
from app.database import get_db

router = APIRouter()

@router.get("/")
def test_endpoint(db=Depends(get_db)):
    try:
        db.execute("SELECT 1")
        return {"message": "API is working and database connection is successful!"}
    except Exception as e:
        return {"error": "Database connection failed!", "details": str(e)}
