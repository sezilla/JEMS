from fastapi import APIRouter
from app.allocator import EventTeamAllocator

router = APIRouter(prefix="/projects", tags=["Projects"])
allocator = EventTeamAllocator()

@router.get("/allocated-teams/{project_name}")
def get_allocated_teams(project_name: str):
    if project_name in allocator.allocated_teams:
        return allocator.allocated_teams[project_name]
    raise HTTPException(status_code=404, detail=f"No allocated teams found for project '{project_name}'")

@router.get("/project-history")
def get_project_history():
    return allocator.project_history
