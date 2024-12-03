from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.schemas import ProjectAllocationRequest
from app.database import get_db
from app.services.allocator import EventTeamAllocator

router = APIRouter()
allocator = EventTeamAllocator()

@router.post("/allocate-teams")
def allocate_teams(request: ProjectAllocationRequest, db: Session = Depends(get_db)):
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
