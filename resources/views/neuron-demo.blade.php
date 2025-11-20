<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuron UI Demo - Mental Prompt</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/mntl.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gradient-bg neuron-bg">
    <!-- Floating Particles Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        @for($i = 0; $i < 20; $i++)
        <div class="neuron-particle" style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 10) }}s;"></div>
        @endfor
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-4xl w-full space-y-8">

            <!-- Header -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-primary mb-2">🧠 Neural Network UI</h1>
                <p class="text-sub">Interactive neuron visualization components</p>
            </div>

            <!-- Neuron Network Visualization -->
            <div class="neuron-network neuron-grid relative">
                <!-- Neural Connections (SVG) -->
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 800 400">
                    <!-- Connection Lines -->
                    <path class="neuron-connection" d="M 100 150 Q 250 100 400 150" />
                    <path class="neuron-connection active" d="M 100 250 Q 250 300 400 250" />
                    <path class="neuron-connection" d="M 400 150 Q 550 100 700 150" />
                    <path class="neuron-connection" d="M 400 250 Q 550 300 700 250" />

                    <!-- Cross Connections -->
                    <path class="neuron-connection" d="M 250 150 Q 325 200 400 250" />
                    <path class="neuron-connection" d="M 250 250 Q 325 200 400 150" />
                </svg>

                <!-- Neuron Nodes -->
                <div class="neuron-node input" style="top: 140px; left: 85px;"></div>
                <div class="neuron-node secondary" style="top: 240px; left: 85px;"></div>

                <div class="neuron-node" style="top: 120px; left: 385px;"></div>
                <div class="neuron-node secondary" style="top: 140px; left: 385px;"></div>
                <div class="neuron-node tertiary" style="top: 160px; left: 385px;"></div>
                <div class="neuron-node" style="top: 220px; left: 385px;"></div>
                <div class="neuron-node secondary" style="top: 240px; left: 385px;"></div>
                <div class="neuron-node tertiary" style="top: 260px; left: 385px;"></div>

                <div class="neuron-node output" style="top: 140px; left: 685px;"></div>
                <div class="neuron-node output" style="top: 240px; left: 685px;"></div>

                <!-- Neural Links Animation -->
                <div class="neural-link delay-1" style="top: 148px;"></div>
                <div class="neural-link delay-2" style="top: 248px;"></div>
                <div class="neural-link delay-3" style="top: 198px;"></div>
            </div>

            <!-- Feature Cards -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="neuron-card">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 flex items-center justify-center">
                            <span class="text-white font-bold">I</span>
                        </div>
                        <div>
                            <h3 class="font-semibold">Input Layer</h3>
                            <p class="text-sm text-sub">Data processing nodes</p>
                        </div>
                    </div>
                    <p class="text-sm">Handles incoming data streams and initial processing with adaptive learning capabilities.</p>
                </div>

                <div class="neuron-card">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-500 flex items-center justify-center">
                            <span class="text-white font-bold">H</span>
                        </div>
                        <div>
                            <h3 class="font-semibold">Hidden Layer</h3>
                            <p class="text-sm text-sub">Pattern recognition</p>
                        </div>
                    </div>
                    <p class="text-sm">Complex pattern recognition and feature extraction through multiple neuron interactions.</p>
                </div>

                <div class="neuron-card">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-red-400 to-orange-500 flex items-center justify-center">
                            <span class="text-white font-bold">O</span>
                        </div>
                        <div>
                            <h3 class="font-semibold">Output Layer</h3>
                            <p class="text-sm text-sub">Decision making</p>
                        </div>
                    </div>
                    <p class="text-sm">Final decision processing and result generation with confidence scoring.</p>
                </div>
            </div>

            <!-- Interactive Demo -->
            <div class="neuron-card">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <span class="mr-2">⚡</span>
                    Interactive Neural Flow
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-sub mb-4">
                            Hover over the neurons above to see the data flow animation. Each connection represents
                            neural pathways transmitting information through the network.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full bg-cyan-400"></div>
                                <span class="text-sm">Active data transmission</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full bg-purple-400"></div>
                                <span class="text-sm">Processing state</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded-full bg-red-400"></div>
                                <span class="text-sm">Error correction</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <div class="text-sm font-mono">
                                <div>Input: [0.8, 0.2, 0.9]</div>
                                <div class="text-cyan-600">→ Processing...</div>
                                <div class="text-green-600">Output: [0.95] ✓</div>
                            </div>
                        </div>
                        <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition-all">
                            Run Neural Simulation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Back to Dashboard -->
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-primary hover:underline">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Alpine.js for interactivity -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('neuronDemo', () => ({
                activeConnections: [],

                init() {
                    // Add hover effects to neurons
                    document.querySelectorAll('.neuron-node').forEach((node, index) => {
                        node.addEventListener('mouseenter', () => {
                            this.activateConnections(index);
                        });
                        node.addEventListener('mouseleave', () => {
                            this.deactivateConnections();
                        });
                    });
                },

                activateConnections(nodeIndex) {
                    // Simulate neural activation
                    document.querySelectorAll('.neuron-connection').forEach((conn, idx) => {
                        if (idx % 2 === nodeIndex % 2) {
                            conn.classList.add('active');
                        }
                    });
                },

                deactivateConnections() {
                    document.querySelectorAll('.neuron-connection').forEach(conn => {
                        conn.classList.remove('active');
                    });
                }
            }));
        });
    </script>
</body>
</html>

