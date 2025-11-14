<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceProvisioningToken;
use Illuminate\Http\Request;

class ProvisioningTokenController extends Controller
{
    public function index()
    {
        $tokens = DeviceProvisioningToken::orderBy('created_at', 'desc')->paginate(50);

        return view('admin.provisioning-tokens.index', compact('tokens'));
    }
}