@extends('layout.master')

@section('konten')
    <div class="mx-auto bg-white rounded-lg shadow-md p-6 animate__animated animate__fadeIn">
        <!-- Input Section -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-start space-x-4">
                <!-- Profile Photo -->
                <img src="{{ asset('asset/profile.webp') }}" alt="Profile"
                    class="w-10 h-10 rounded-full object-cover shrink-0">

                <div class="flex-grow relative">
                    <form action="{{ route('ecoforum.store') }}" method="POST" enctype="multipart/form-data"
                        class="flex items-center space-x-3">
                        @csrf
                        @method('POST')

                        <!-- Discussion Input -->
                        <textarea name="content" id="discussion-box" rows="1"
                            class="flex-grow p-3 border border-gray-300 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-green-400 text-sm"
                            placeholder="Start a discussion"></textarea>

                        <!-- File Upload -->
                        <label for="file-upload" class="cursor-pointer text-gray-500 hover:text-green-600">
                            <i class="fa-regular fa-image text-xl"></i>
                            <input type="file" name="image" id="file-upload" class="hidden" accept="image/*">
                        </label>

                        <!-- Post Button -->
                        <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-full text-sm hover:bg-green-700 transition-colors">
                            Post
                        </button>
                    </form>
                </div>

            </div>
            <div id="image-upload-container" class="hidden mt-4">
                <img id="uploaded-image" class="w-full h-96 object-contain rounded-lg">
            </div>
        </div>

        <!-- Discussion Section -->
        <div class="mt-6">
            @foreach ($posts as $post)
                <div class="flex items-start gap-3 mb-6">
                    <img src="{{ asset('asset/profile.webp') }}" alt="User" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h4 class="text-lg text-gray-800 mb-2">{{ $post->buyer->name }}</h4>
                        <p class="text-gray-600 text-sm mb-4">{{ $post->content }}</p>
                        <img src="{{ asset($post->image) }}" alt="">
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <a href="#" class="hover:text-gray-700"
                                onclick="openCommentsModal('{{ $post->id }}')">
                                <i class="fa-regular fa-comment"></i>
                            </a>
                            <span onclick="toggleLike(this, '{{ $post->id }}')"
                                class="cursor-pointer flex items-center gap-2">
                                @if ($post->likes()->where('buyer_id', session('buyer')->id)->exists())
                                    <i class="fa-solid fa-heart"></i>
                                @else
                                    <i class="fa-regular fa-heart"></i>
                                @endif
                                <span id="like-count-{{ $post->id }}">{{ $post->like }}</span>
                            </span>
                            <a href="#" onclick="openShareModal('{{ route('ecoforum.show', $post->id) }}')"
                                class="hover:text-gray-700">
                                <i class="fa-solid fa-share"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Comments Modal -->
    <div id="comments-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="bg-white rounded-lg w-96 max-h-[80vh] flex flex-col">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Comments</h3>
                <button id="close-comments-modal" class="text-gray-600 hover:text-gray-800">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div id="comments-container" class="p-4 overflow-y-auto flex-grow">
                <!-- Comments will be dynamically loaded here -->
            </div>

            <form id="reply-form" class="p-4 border-t border-gray-200">
                @csrf
                <input type="hidden" name="post_id" id="current-post-id">
                <textarea name="content" class="w-full p-2 border border-gray-300 rounded-lg mb-2" placeholder="Write a reply..."
                    required></textarea>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-full">Reply</button>
            </form>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="share-modal" class="fixed inset-0 bg-white-800 bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg w-96 shadow-lg">
            <h3 class="text-lg font-semibold mb-4">Share This Post</h3>
            <div class="flex items-center gap-2">
                <input type="text" id="share-link" class="w-full p-3 border border-gray-300 rounded-lg" readonly>
                <!-- Copy Icon -->
                <button onclick="copyLink()" class="text-gray-600 hover:text-gray-800">
                    <i class="fa-solid fa-copy text-xl"></i>
                </button>
            </div>
            <div class="mt-4 flex justify-end">
                <button id="close-modal"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-400">Close</button>
            </div>
        </div>
    </div>

    <script>
        const fileUpload = document.getElementById('file-upload');
        const imageUploadContainer = document.getElementById('image-upload-container');
        const uploadedImage = document.getElementById('uploaded-image');

        fileUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadedImage.src = e.target.result;
                    imageUploadContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        const textarea = document.getElementById('discussion-box');
        const inputContainer = document.getElementById('input-container');
        textarea.addEventListener('input', function() {
            this.style.height = '40px';
            this.style.height = this.scrollHeight + 'px';

            if (!imagePreview.classList.contains('hidden')) {
                inputContainer.style.height = 'auto';
            }
        });

        function toggleLike(element, postId) {
            fetch(`/ecoforum/${postId}/toggle-like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const icon = element.querySelector('i');
                    const likeCountElement = document.getElementById(`like-count-${postId}`);

                    if (data.status === 'liked') {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        likeCountElement.textContent = parseInt(likeCountElement.textContent) + 1;
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        likeCountElement.textContent = parseInt(likeCountElement.textContent) - 1;
                    }
                });
        }

        // Comments Modal
        function openCommentsModal(postId) {
            const commentsModal = document.getElementById('comments-modal');
            const commentsContainer = document.getElementById('comments-container');
            const currentPostIdInput = document.getElementById('current-post-id');

            // Set the current post ID
            currentPostIdInput.value = postId;

            // Fetch comments directly when opening the modal
            fetch(`/ecoforum/${postId}/comments`, {
                    method: 'POST', // Changed to POST as per your route
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(comments => {
                    // Clear previous comments
                    commentsContainer.innerHTML = '';

                    // If no comments
                    if (comments.length === 0) {
                        commentsContainer.innerHTML = '<p class="text-center text-gray-500">No comments yet</p>';
                    } else {
                        // Populate comments
                        comments.forEach(comment => {
                            const commentElement = document.createElement('div');
                            commentElement.classList.add('flex', 'items-start', 'gap-3', 'mb-4');
                            commentElement.innerHTML = `
                                <img src="{{ asset('asset/profile.webp') }}" alt="User" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h5 class="text-sm text-gray-800">${comment.buyer_name}</h5>
                                    <p class="text-sm text-gray-600">${comment.comment}</p>
                                </div>
                            `;
                            commentsContainer.appendChild(commentElement);
                        });
                    }

                    // Show the modal
                    commentsModal.classList.remove('hidden');
                });
        }

        // Reply Form Submission
        document.getElementById('reply-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const postId = document.getElementById('current-post-id').value;
            const content = this.querySelector('textarea[name="content"]').value;

            fetch(`/ecoforum/${postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        content: content,
                        post_id: postId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Reload comments after successful reply
                    openCommentsModal(postId);
                    // Clear textarea
                    this.querySelector('textarea[name="content"]').value = '';
                });
        });

        // Share Modal Functions
        function openShareModal(link) {
            // Set the link in the input field
            document.getElementById('share-link').value = link;

            // Show the modal
            document.getElementById('share-modal').classList.remove('hidden');
        }

        // Close the Share Modal
        document.getElementById('close-comments-modal').addEventListener('click', function() {
            document.getElementById('comments-modal').classList.add('hidden');
        });

        function copyLink() {
            const linkInput = document.getElementById('share-link');
            linkInput.select();
            document.execCommand('copy'); // For older browsers (fallback)

            // Modern Clipboard API
            navigator.clipboard.writeText(linkInput.value)
                .then(() => {
                    alert("Link copied to clipboard!");
                })
                .catch(err => {
                    console.error("Failed to copy: ", err);
                });
        }
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
@endsection