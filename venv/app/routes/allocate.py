from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from app.database import get_db
from app.allocator import EventTeamAllocator
from app.schemas import ProjectAllocationRequest

router = APIRouter(prefix="/allocate", tags=["Allocate"])
allocator = EventTeamAllocator()

@router.post("/teams")
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
        raise HTTPException(status_code=400, detail=f"Error: {str(e)}")
