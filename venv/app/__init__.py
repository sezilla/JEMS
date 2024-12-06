from fastapi import FastAPI
from app.routers import allocation, history, test

app = FastAPI()

# Include Routers
app.include_router(allocation.router, prefix="/allocation", tags=["Allocation"])
app.include_router(history.router, prefix="/history", tags=["History"])
app.include_router(test.router, prefix="/test", tags=["Test"])
