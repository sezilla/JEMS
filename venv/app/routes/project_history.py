from fastapi import APIRouter
from app.routes.team_allocation import allocator

router = APIRouter()

@router.get("/")
def get_project_history():
    return allocator.project_history
