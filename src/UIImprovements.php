<?php
// UI/UX improvement utilities
class UIImprovements {
    
    // Accessibility helper
    public static function generateAccessibleForm($fields, $data = []) {
        $html = '';
        
        foreach ($fields as $name => $config) {
            $id = self::generateId($name);
            $value = $data[$name] ?? $config['default'] ?? '';
            $required = $config['required'] ?? false;
            $label = $config['label'] ?? ucwords(str_replace('_', ' ', $name));
            
            $html .= '<div class="form-group mb-3">';
            
            // Label with required indicator
            $html .= '<label for="' . $id . '" class="form-label">';
            $html .= Security::escape($label);
            if ($required) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</label>';
            
            // Input field
            $html .= self::generateInput($name, $config, $id, $value, $required);
            
            // Help text
            if (isset($config['help'])) {
                $html .= '<small class="form-text text-muted">' . Security::escape($config['help']) . '</small>';
            }
            
            // Validation feedback
            $html .= '<div class="invalid-feedback" id="' . $id . '-feedback"></div>';
            
            $html .= '</div>';
        }
        
        return $html;
    }
    
    private static function generateInput($name, $config, $id, $value, $required) {
        $type = $config['type'] ?? 'text';
        $attributes = [
            'id' => $id,
            'name' => $name,
            'class' => 'form-control',
            'value' => Security::escape($value)
        ];
        
        if ($required) {
            $attributes['required'] = 'required';
        }
        
        if (isset($config['placeholder'])) {
            $attributes['placeholder'] = Security::escape($config['placeholder']);
        }
        
        if (isset($config['maxlength'])) {
            $attributes['maxlength'] = $config['maxlength'];
        }
        
        if (isset($config['pattern'])) {
            $attributes['pattern'] = $config['pattern'];
        }
        
        switch ($type) {
            case 'textarea':
                $attributes['rows'] = $config['rows'] ?? 3;
                $html = '<textarea';
                foreach ($attributes as $attr => $val) {
                    if ($attr !== 'value') {
                        $html .= ' ' . $attr . '="' . $val . '"';
                    }
                }
                $html .= '>' . Security::escape($value) . '</textarea>';
                break;
                
            case 'select':
                $html = '<select';
                foreach ($attributes as $attr => $val) {
                    if ($attr !== 'value') {
                        $html .= ' ' . $attr . '="' . $val . '"';
                    }
                }
                $html .= '>';
                
                if (isset($config['options'])) {
                    foreach ($config['options'] as $optValue => $optLabel) {
                        $selected = $optValue == $value ? 'selected' : '';
                        $html .= '<option value="' . Security::escape($optValue) . '" ' . $selected . '>';
                        $html .= Security::escape($optLabel);
                        $html .= '</option>';
                    }
                }
                
                $html .= '</select>';
                break;
                
            case 'checkbox':
                $html = '<div class="form-check">';
                $html .= '<input type="checkbox" class="form-check-input" id="' . $id . '" name="' . $name . '" value="1"';
                if ($value) {
                    $html .= ' checked';
                }
                if ($required) {
                    $html .= ' required';
                }
                $html .= '>';
                $html .= '<label class="form-check-label" for="' . $id . '">' . Security::escape($config['checkbox_label'] ?? '') . '</label>';
                $html .= '</div>';
                break;
                
            default:
                $html = '<input type="' . Security::escape($type) . '"';
                foreach ($attributes as $attr => $val) {
                    $html .= ' ' . $attr . '="' . $val . '"';
                }
                $html .= '>';
                break;
        }
        
        return $html;
    }
    
    // Generate responsive tables
    public static function generateResponsiveTable($headers, $data, $actions = []) {
        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-striped table-hover">';
        
        // Header
        $html .= '<thead class="table-dark"><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . Security::escape($header) . '</th>';
        }
        if (!empty($actions)) {
            $html .= '<th>Actions</th>';
        }
        $html .= '</tr></thead>';
        
        // Body
        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . Security::escape($cell) . '</td>';
            }
            
            if (!empty($actions)) {
                $html .= '<td>';
                foreach ($actions as $action) {
                    $url = str_replace('{id}', $row['id'] ?? '', $action['url']);
                    $html .= '<a href="' . $url . '" class="btn btn-sm btn-' . ($action['class'] ?? 'primary') . ' me-1">';
                    $html .= Security::escape($action['label']);
                    $html .= '</a>';
                }
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    // Generate alerts
    public static function generateAlert($message, $type = 'info', $dismissible = true) {
        $classes = [
            'info' => 'alert-info',
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'danger' => 'alert-danger',
            'error' => 'alert-danger'
        ];
        
        $html = '<div class="alert ' . ($classes[$type] ?? 'alert-info') . '"';
        if ($dismissible) {
            $html .= ' role="alert"';
        }
        $html .= '>';
        
        if ($dismissible) {
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        }
        
        $html .= Security::escape($message);
        $html .= '</div>';
        
        return $html;
    }
    
    // Generate loading spinner
    public static function generateSpinner($size = 'sm') {
        $sizes = [
            'sm' => 'spinner-border-sm',
            'md' => '',
            'lg' => 'spinner-border-lg'
        ];
        
        return '<div class="spinner-border ' . ($sizes[$size] ?? '') . '" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>';
    }
    
    // Generate progress bar
    public static function generateProgressBar($percentage, $label = '', $animated = true) {
        $html = '<div class="progress">';
        $html .= '<div class="progress-bar';
        if ($animated) {
            $html .= ' progress-bar-striped progress-bar-animated';
        }
        $html .= '" role="progressbar" style="width: ' . $percentage . '%" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">';
        if ($label) {
            $html .= Security::escape($label);
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    // Generate breadcrumb
    public static function generateBreadcrumb($items) {
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';
        
        foreach ($items as $index => $item) {
            $active = $index === count($items) - 1;
            $html .= '<li class="breadcrumb-item';
            if ($active) {
                $html .= ' active" aria-current="page';
            }
            $html .= '">';
            
            if (!$active && isset($item['url'])) {
                $html .= '<a href="' . Security::escape($item['url']) . '">' . Security::escape($item['label']) . '</a>';
            } else {
                $html .= Security::escape($item['label']);
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ol>';
        $html .= '</nav>';
        
        return $html;
    }
    
    // Generate card
    public static function generateCard($title, $content, $footer = '', $class = '') {
        $html = '<div class="card ' . Security::escape($class) . '">';
        
        if ($title) {
            $html .= '<div class="card-header">';
            $html .= '<h5 class="card-title mb-0">' . Security::escape($title) . '</h5>';
            $html .= '</div>';
        }
        
        $html .= '<div class="card-body">';
        $html .= $content;
        $html .= '</div>';
        
        if ($footer) {
            $html .= '<div class="card-footer">';
            $html .= $footer;
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    private static function generateId($name) {
        return 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
    }
    
    // Generate accessible navigation
    public static function generateNavigation($items, $active = '') {
        $html = '<nav class="navbar navbar-expand-lg navbar-dark bg-primary" aria-label="Main navigation">';
        $html .= '<div class="container-fluid">';
        $html .= '<a class="navbar-brand" href="/ksp_peb/">Koperasi</a>';
        $html .= '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">';
        $html .= '<span class="navbar-toggler-icon"></span>';
        $html .= '</button>';
        $html .= '<div class="collapse navbar-collapse" id="navbarNav">';
        $html .= '<ul class="navbar-nav me-auto">';
        
        foreach ($items as $item) {
            $isActive = $item['url'] === $active;
            $html .= '<li class="nav-item">';
            $html .= '<a class="nav-link';
            if ($isActive) {
                $html .= ' active';
            }
            $html .= '" href="' . Security::escape($item['url']) . '"';
            if ($isActive) {
                $html .= ' aria-current="page"';
            }
            $html .= '>' . Security::escape($item['label']) . '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</nav>';
        
        return $html;
    }
}
