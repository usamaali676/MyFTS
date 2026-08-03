@extends('layouts.dashboard')
@section('content')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Trainee/{{ $trainee->name }}/</span> Comments
                </h4>
                <div>
                    <a href="{{ route('trainee.index') }}" class="btn btn-outline-secondary me-2">Back to Trainees</a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addComment">Add Comment</button>
                </div>
              </div>

              <div class="card">
                <div class="card-body">
                    @forelse ($comments as $comment)
                        <div class="d-flex align-items-start mb-4 pb-4 border-bottom">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $comment->user->name ?? 'Unknown' }}</h6>
                                    <small class="text-muted">{{ $comment->comment_date->format('M d, Y h:i A') }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No comments yet.</p>
                    @endforelse
                </div>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>

          <div class="modal fade" id="addComment" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('traineecomment.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
                        <div class="modal-body">
                            <div class="form-floating form-floating-outline">
                                <textarea name="comment" id="comment" class="form-control" style="height: 120px" placeholder="Comment"></textarea>
                                <label for="comment">Comment</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Comment</button>
                        </div>
                    </form>
                </div>
            </div>
          </div>
@endsection
