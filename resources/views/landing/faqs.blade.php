<x-landing-layout>
    <div class="mx-auto px-20 bg-gradient-to-b from-gray-100 to-blue-300 h-full">
        <div class="mt-16 text-center mb-4">
            <h1 class="text-4xl font-black">Frequently Asked Questions</h1>
        </div>
        <div class="space-y-4">
            <div class="faq-item border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden">
                <button type="button" class="faq-toggle w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50">
                    <span>What is OnePage?</span>
                    <i class="faq-icon fa-solid fa-chevron-down transition-transform duration-300"></i>
                </button>
                <div class="faq-content hidden px-5 pb-4 text-gray-600">
                    OnePage is a document management system designed by FCU Solutions Inc. to streamline document creation, review, approval, monitoring, and storage.
                </div>
            </div>

            <div class="faq-item border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden">
                <button type="button" class="faq-toggle w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50">
                    <span>Who can use OnePage?</span>
                    <i class="faq-icon fa-solid fa-chevron-down transition-transform duration-300"></i>
                </button>
                <div class="faq-content hidden px-5 pb-4 text-gray-600">
                    Authorized users within the organization such as authors, reviewers, approvers, and document controllers can use the system based on their assigned roles and permissions.
                </div>
            </div>

            <div class="faq-item border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden">
                <button type="button" class="faq-toggle w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50">
                    <span>Can I track document status in real time?</span>
                    <i class="faq-icon fa-solid fa-chevron-down transition-transform duration-300"></i>
                </button>
                <div class="faq-content hidden px-5 pb-4 text-gray-600">
                    Yes, OnePage allows users to monitor document progress and view the current status throughout the workflow.
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const item = button.closest('.faq-item');
                const content = item.querySelector('.faq-content');
                const icon = item.querySelector('.faq-icon');

                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });
    </script>
</x-landing-layout>