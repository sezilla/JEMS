from fastapi import FastAPI
from app.routers import allocation, history, test

app = FastAPI()

app.include_router(allocation.router)
app.include_router(history.router)
app.include_router(test.router)
