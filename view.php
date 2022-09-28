<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0,user-scalable=0">
    <meta name="description" content="">
    <title>3D Viewer</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body style="background-color:#000000;">

<div class="progress-bar-container">
    <label for="progress-bar">Loading...</label>
    <progress id="progress-bar" value="0" max="100"></progress>
</div>

<script type="module">
    import * as THREE from '/js/3d/three.module.js';
    import {VRButton } from '/js/3d/VRButton.js';
    import {OrbitControls} from '/js/3d/OrbitControls.js';
    import {GLTFLoader} from '/js/3d/GLTFLoader.js';

    // global 3D scene variables
    let camera, scene, renderer, controls, container;

    const progressBar = document.getElementById("progress-bar");
    const progressBarContainer = document.querySelector(".progress-bar-container");

    function initScene() {
        // create DIV for the canvas
        container = document.createElement( 'div' );
        document.body.appendChild( container );

        // Scene Setup
        scene = new THREE.Scene();

        var width  = window.innerWidth,
            height = window.innerHeight;

        // Camera setup
        const fov = 30;
        const near = 0.04;
        const far = 2000;

        camera = new THREE.PerspectiveCamera(fov, width / height, near, far);
        camera.position.set(0,0,10);

        // Renderer Setup
        renderer = new THREE.WebGLRenderer( { antialias: true } );
        renderer.physicallyCorrectLights = true;
        renderer.setPixelRatio( window.devicePixelRatio );
        renderer.setSize( width, height );
        renderer.xr.enabled = true;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;
        renderer.outputEncoding = THREE.sRGBEncoding;

        container.appendChild(renderer.domElement);

        // Controls Setup
        controls = new OrbitControls(camera, renderer.domElement);
        controls.target.set(0, 0, -5);
        controls.update();

        // Loading Manager to see when the model is loaded
        THREE.DefaultLoadingManager.onStart = (url, loaded, total) => {
            console.log(`loading ${url}.  loaded ${loaded} of ${total}`)
        }
        THREE.DefaultLoadingManager.onLoad = () => {
            console.log(`loading complete`)
            progressBarContainer.style.display = "none";
        }
        THREE.DefaultLoadingManager.onProgress = (url, loaded, total) => {
            // progressBar.value = 100*(loaded/total);
        }
        THREE.DefaultLoadingManager.onError = (url) => {
            console.log(`error loading ${url}`)
        }

        loadModel();

        // document.body.appendChild( VRButton.createButton( renderer ) );
        renderer.setAnimationLoop( function () {
            if(renderer.xr.isPresenting)
                render();
        } );

        window.addEventListener( 'resize', onWindowResize );
    }

    function loadModel() {
        var modelname = "<?php echo $_GET['model'] ?>";
        var modelformat = "<?php echo $_GET['format'] ?>";
        var modelpath = 'models/' + modelname + '.' + modelformat;

        // add some general lights
        scene.add(new THREE.AmbientLight( 0xFFFFFF, 0.7 ));

        // add a directional light for the shadows
        const light = new THREE.DirectionalLight(0xFFFFFF, 3);
        light.position.set(30, 30, 33);
        light.target.position.set(-5, 0, 0);
        scene.add(light);

        new GLTFLoader(  )
            .load(modelpath, function ( gltf ) {
                scene.add( gltf.scene );
                render();
            } , function(request) {
                progressBar.value = 100*(request.loaded/request.total);
            } );

        // position and point the camera
        camera.lookAt( scene.position );

        render();

        function requestRenderIfNotRequested() {
            console.log("requestRenderIfNotRequested");
            if (!renderRequested) {
                console.log("requestRenderIfNotRequested true");
                renderRequested = true;
                requestAnimationFrame(render);
            }
        }

        controls.addEventListener('change', requestRenderIfNotRequested);
    }

    let renderRequested = false;

    function resizeRendererToDisplaySize(renderer) {
        const canvas = renderer.domElement;
        const width = canvas.clientWidth;
        const height = canvas.clientHeight;
        const needResize = canvas.width !== width || canvas.height !== height;
        if (needResize) {
            renderer.setSize(width, height, false);
        }
        return needResize;
    }

    function render() {
        renderRequested = undefined;
        if (resizeRendererToDisplaySize(renderer)) {
            const canvas = renderer.domElement;
            camera.aspect = canvas.clientWidth / canvas.clientHeight;
            camera.updateProjectionMatrix();
        }
        controls.update();
        renderer.shadowMap.enabled = true;
        renderer.render(scene, camera);
    }

    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize( window.innerWidth, window.innerHeight );
        render();
    }

    initScene();
</script>

</body>
</html>
